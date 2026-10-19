@echo off
title WhatsApp Local Gateway Service - Clinic System
echo ========================================================
echo   Starting WhatsApp Local Service (Baileys Engine)...
echo ========================================================
cd /d "%~dp0whatsapp-service"
node server.js
pause
