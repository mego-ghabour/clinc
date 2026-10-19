const {
  default: makeWASocket,
  DisconnectReason,
  useMultiFileAuthState,
  fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');
const express = require('express');
const QRCode = require('qrcode');
const pino = require('pino');
const path = require('path');
const fs = require('fs');

// Catch-all handlers to guarantee service never crashes from background socket rejections
process.on('uncaughtException', (err) => {
  console.error('[WhatsApp Service] Handled Uncaught Exception:', err.message || err);
});

process.on('unhandledRejection', (reason) => {
  console.error('[WhatsApp Service] Handled Unhandled Rejection:', reason?.message || reason);
});

const app = express();
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Allow CORS
app.use((req, res, next) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
  if (req.method === 'OPTIONS') {
    return res.sendStatus(200);
  }
  next();
});

const PORT = process.env.PORT || 3000;
const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

let sock = null;
let isConnecting = false;
let connectionState = 'connecting';
let currentQrRaw = null;
let currentQrBase64 = null;
let connectedUser = null;

async function cleanupSocket() {
  if (sock) {
    try {
      sock.ev?.removeAllListeners();
      sock.end?.(undefined);
    } catch (e) {}
    sock = null;
  }
}

async function connectToWhatsApp() {
  if (isConnecting) return;
  isConnecting = true;

  try {
    await cleanupSocket();

    if (!fs.existsSync(AUTH_DIR)) {
      fs.mkdirSync(AUTH_DIR, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
    let version = [2, 3000, 1015901307];
    try {
      const verRes = await fetchLatestBaileysVersion();
      if (verRes?.version) version = verRes.version;
    } catch (e) {}

    sock = makeWASocket({
      version,
      auth: state,
      logger: pino({ level: 'silent' }),
      browser: ['Clinic System', 'Chrome', '124.0.0.0'],
      syncFullHistory: false,
      connectTimeoutMs: 60000,
      defaultQueryTimeoutMs: 60000,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr) {
        currentQrRaw = qr;
        try {
          currentQrBase64 = await QRCode.toDataURL(qr, { margin: 2, scale: 6 });
          connectionState = 'qr';
          console.log('[WhatsApp Service] New QR Code generated successfully. Ready to scan.');
        } catch (err) {
          console.error('[WhatsApp Service] Error generating QR data URL:', err);
        }
      }

      if (connection === 'close') {
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const isLoggedOut = statusCode === DisconnectReason.loggedOut;
        console.log(`[WhatsApp Service] Connection closed (code: ${statusCode}). Logged out: ${isLoggedOut}`);

        connectionState = 'disconnected';
        currentQrRaw = null;
        currentQrBase64 = null;
        connectedUser = null;

        await cleanupSocket();

        if (isLoggedOut) {
          console.log('[WhatsApp Service] Session terminated by WhatsApp. Resetting auth directory for fresh login.');
          try {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
          } catch (e) {}
        }

        setTimeout(() => {
          isConnecting = false;
          connectToWhatsApp();
        }, isLoggedOut ? 2000 : 5000);

      } else if (connection === 'open') {
        connectionState = 'open';
        currentQrRaw = null;
        currentQrBase64 = null;
        connectedUser = sock.user;
        const phone = sock.user?.id ? sock.user.id.split(':')[0] : 'Unknown';
        console.log(`[WhatsApp Service] Connection opened! Linked Phone: +${phone}`);
      }
    });

  } catch (err) {
    console.error('[WhatsApp Service] Unexpected error in connectToWhatsApp:', err.message || err);
    await cleanupSocket();
    setTimeout(() => {
      isConnecting = false;
      connectToWhatsApp();
    }, 5000);
  } finally {
    isConnecting = false;
  }
}

// 1. Health & Connection Status
app.get('/status', (req, res) => {
  const phone = connectedUser?.id ? connectedUser.id.split(':')[0] : null;
  return res.json({
    state: connectionState,
    connected: connectionState === 'open',
    phone: phone,
    user: connectedUser,
    hasQr: !!currentQrBase64,
  });
});

// 2. Fetch QR Code JSON (base64)
app.get('/qr', (req, res) => {
  if (connectionState === 'open') {
    return res.json({
      status: 'connected',
      message: 'WhatsApp is already connected.',
      phone: connectedUser?.id ? connectedUser.id.split(':')[0] : null,
    });
  }

  if (currentQrBase64) {
    return res.json({
      status: 'qr_ready',
      qr: currentQrBase64,
      raw: currentQrRaw,
    });
  }

  return res.json({
    status: 'waiting',
    message: 'جاري توليد كود الـ QR من سيرفرات واتساب، يرجى الانتظار بضع ثوانٍ...',
  });
});

// 3. Directly stream PNG image of the QR Code
app.get('/qr/image', (req, res) => {
  if (currentQrRaw) {
    res.setHeader('Content-Type', 'image/png');
    return QRCode.toFileStream(res, currentQrRaw, { margin: 2, scale: 6 });
  }

  res.status(404).send('No QR code ready yet.');
});

// 4. Send WhatsApp Message
app.post('/send', async (req, res) => {
  const { phone, message, body } = req.body;
  const msgText = message || body;

  if (!phone || !msgText) {
    return res.status(400).json({
      success: false,
      message: 'رقم الهاتف والرسالة مطلوبان.',
    });
  }

  if (connectionState !== 'open' || !sock) {
    return res.status(503).json({
      success: false,
      message: 'جلسة الواتساب غير متصلة حالياً. يرجى مسح رمز الـ QR أولاً.',
    });
  }

  // Format phone number
  let cleaned = phone.replace(/[^0-9]/g, '');
  if (cleaned.startsWith('05') && cleaned.length === 10) {
    cleaned = '966' + cleaned.substring(1);
  } else if (cleaned.startsWith('01') && cleaned.length === 11) {
    cleaned = '20' + cleaned.substring(1);
  }

  const jid = `${cleaned}@s.whatsapp.net`;

  try {
    const result = await sock.sendMessage(jid, { text: msgText });
    return res.json({
      success: true,
      message: 'تم إرسال الرسالة بنجاح عبر الواتساب.',
      messageId: result.key.id,
    });
  } catch (err) {
    console.error('[WhatsApp Service] Error sending message:', err.message || err);
    return res.status(500).json({
      success: false,
      message: 'فشل إرسال الرسالة: ' + (err.message || 'خطأ غير معروف'),
    });
  }
});

// 5. Logout & Disconnect
app.post('/logout', async (req, res) => {
  try {
    if (sock) {
      await sock.logout();
    }
  } catch (e) {}

  await cleanupSocket();

  try {
    fs.rmSync(AUTH_DIR, { recursive: true, force: true });
  } catch (e) {}

  connectionState = 'disconnected';
  connectedUser = null;
  currentQrRaw = null;
  currentQrBase64 = null;

  setTimeout(() => {
    isConnecting = false;
    connectToWhatsApp();
  }, 1500);

  return res.json({
    success: true,
    message: 'تم تسجيل الخروج وفصل الجلسة بنجاح.',
  });
});

const HOST = process.env.HOST || '0.0.0.0';
app.listen(PORT, HOST, () => {
  console.log(`[WhatsApp Service] WhatsApp Baileys service listening on http://${HOST}:${PORT}`);
  connectToWhatsApp();
});
