# 📲 WhatsApp Microservice (Baileys Engine) for Clinic System

خادم مصغر مبني بـ Node.js ومكتبة `@whiskeysockets/baileys` لإرسال واستقبال رسائل وتنبيهات الواتساب عبر بروتوكول WhatsApp Multi-Device الرسمي بدون أي وسيط ولا رسوم شهرية.

---

## 🚀 طريقة الرفع والتشغيل على منصة سحابية مجانية (Render.com)

إذا كان موقع العيادة الرئيسي على **استضافة مشتركة (Shared Hosting)** وتريد تشغيل خدمة الواتساب على **السحابة (Cloud)** مجاناً 100%:

### الخطوة 1: التسجيل في Render
1. ادخل إلى **[https://render.com](https://render.com)** وسجل حساباً مجانياً (عبر GitHub أو البريد الإلكتروني).

### الخطوة 2: إنشاء خدمة الويب (Web Service)
1. اضغط على **New +** ثم اختر **Web Service**.
2. اختر **Deploy from Git repository** (أو ارفع المجلد عبر GitHub).
3. اضبط الإعدادات التالية:
   - **Name:** `clinic-whatsapp` (أو أي اسم تفضله).
   - **Language:** `Node`.
   - **Build Command:** `npm install`.
   - **Start Command:** `node server.js`.
   - **Instance Type:** `Free`.
4. اضغط على **Create Web Service**.

### الخطوة 3: الحصول على الرابط وربطه بنظام العيادة
1. بعد دقيقتين ستعطيك Render رابطاً آمناً يبدأ بـ HTTPS، مثل:
   `https://clinic-whatsapp-xxxx.onrender.com`
2. افتح لوحة تحكم عيادتك على الاستضافة المشتركة:
   - اذهب إلى **الإعدادات (Settings)** -> **إشعارات الواتساب**.
   - في خانة **رابط خادم الواتساب (سحابي أو محلي)**، الصق الرابط:
     `https://clinic-whatsapp-xxxx.onrender.com`
   - اضغط **حفظ الإعدادات**.
3. ستجد رمز الـ QR ظهر فوراً في شاشة العيادة، امسحه من هاتفك وستتصل الخدمة فوراً!

---

## ⚙️ نقاط النهاية المتوفرة (API Endpoints)
* `GET /status` : فحص حالة الاتصال ورقم الهاتف المرتبط.
* `GET /qr` : جلب رمز الـ QR بصيغة Base64.
* `POST /send` : إرسال رسالة واتساب برقم الهاتف والنص `{ phone, message }`.
* `POST /logout` : فصل الجلسة وإعادة توليد رمز جديد.
