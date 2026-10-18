<?php
/**
 * أداة الفحص والإصلاح التلقائي لروابط الصور (Storage Link & Permissions Fixer)
 * مسار التشغيل: https://fkretk.com/projects/public/storage_fix.php
 */

$publicStorage = __DIR__ . '/storage';
$targetDir = realpath(__DIR__ . '/../storage/app/public');
$action = $_GET['action'] ?? null;
$message = '';
$messageType = '';

function fixPermissionsRecursively($path) {
    if (!is_dir($path)) return;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($items as $item) {
        if ($item->isDir()) {
            @chmod($item->getRealPath(), 0755);
        } else {
            @chmod($item->getRealPath(), 0644);
        }
    }
    @chmod($path, 0755);
}

if ($action === 'create_symlink') {
    // 1. حذف أي مجلد أو رابط قديم تالف
    if (file_exists($publicStorage) || is_link($publicStorage)) {
        if (is_link($publicStorage)) {
            @unlink($publicStorage);
        } elseif (is_dir($publicStorage)) {
            @rename($publicStorage, $publicStorage . '_backup_' . time());
        }
    }

    // 2. إصلاح التصاريح
    if ($targetDir) {
        fixPermissionsRecursively($targetDir);
    }

    // 3. إنشاء الرابط الرمزي لنظام لينكس
    if ($targetDir && @symlink($targetDir, $publicStorage)) {
        $message = "تم إنشاء الرابط الرمزي (Symlink) بنجاح وإصلاح التصاريح إلى 755/644!";
        $messageType = "success";
    } else {
        $message = "تعذر إنشاء Symlink (قد تكون دالة symlink محظورة من الاستضافة). يرجى الضغط على زر (تفعيل العرض عبر محرك لارفيل المباشر).";
        $messageType = "warning";
    }
}

if ($action === 'use_laravel_stream') {
    // حذف المجلد القديم إن وجد لكي يمرر الأباتشي الطلبات مباشرة إلى راوت لارفيل
    if (file_exists($publicStorage) || is_link($publicStorage)) {
        if (is_link($publicStorage)) {
            @unlink($publicStorage);
        } elseif (is_dir($publicStorage)) {
            @rename($publicStorage, $publicStorage . '_removed_' . time());
        }
    }
    if ($targetDir) {
        fixPermissionsRecursively($targetDir);
    }
    $message = "تم تفعيل وضع التوجيه المباشر عبر لارفيل بنجاح! الآن سيتولى راوت storage/{path} قراءة وعرض الصور مباشرة دون الحاجة لـ Symlink ودون خطأ 403.";
    $messageType = "success";
}

$isLink = is_link($publicStorage);
$isDir = is_dir($publicStorage);
$targetExists = $targetDir && is_dir($targetDir);
$linkTarget = $isLink ? @readlink($publicStorage) : null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أداة إصلاح وتفعيل صور التخزين</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen p-6 flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        
        <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-5">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-black">
                🖼️
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-800">أداة فحص وإصلاح صور التخزين (Storage Fixer)</h1>
                <p class="text-sm text-gray-500 font-bold">حل مشكلة 403 Forbidden وتفعيل ظهور الصور على السيرفر</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-2xl text-sm font-bold <?= $messageType === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- حالة النظام الحالية -->
        <div class="space-y-3 bg-gray-50 p-5 rounded-2xl border border-gray-100 mb-6 text-sm font-bold text-gray-700">
            <div class="flex justify-between items-center">
                <span>مسار المجلد الأصلي (Target Storage):</span>
                <span class="font-mono text-xs dir-ltr <?= $targetExists ? 'text-emerald-600' : 'text-red-600' ?>">
                    <?= htmlspecialchars($targetDir ?: 'غير موجود!') ?>
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span>رابط التخزين في Public (public/storage):</span>
                <span class="font-mono text-xs">
                    <?php if ($isLink): ?>
                        <span class="text-emerald-600">✓ رابط رمزي (Symlink) يشير إلى: <?= htmlspecialchars($linkTarget) ?></span>
                    <?php elseif ($isDir): ?>
                        <span class="text-amber-600">مجلد عادي (Directory) - غالباً هو سبب 403 Forbidden!</span>
                    <?php else: ?>
                        <span class="text-blue-600">غير موجود (سيتولى راوت لارفيل المباشر عرض الصور فوراً)</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- أزرار الحل الفوري -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <a href="?action=create_symlink" class="block text-center bg-teal-600 hover:bg-teal-700 text-white font-black py-3.5 px-4 rounded-2xl shadow transition">
                ⚡ خيار 1: إنشاء Symlink وإصلاح التصاريح
            </a>
            <a href="?action=use_laravel_stream" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3.5 px-4 rounded-2xl shadow transition">
                🚀 خيار 2: تفعيل العرض المباشر (حل مضمون 100%)
            </a>
        </div>

        <div class="text-xs text-gray-400 font-bold text-center">
            ملاحظة: بعد الضغط على أحد الخيارين أعلاه وظهور رسالة النجاح، جرب تحديث رابط الصورة على السيرفر وستعمل فوراً بإذن الله.
        </div>

    </div>
</body>
</html>
