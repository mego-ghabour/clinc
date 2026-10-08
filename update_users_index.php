<?php
$content = file_get_contents("resources/views/users/index.blade.php");

// Replace avatar circle in table
$content = preg_replace(
    "/<div class=\"w-10 h-10 rounded-full(.*?)>.*?<\/div>/s",
    "<div class=\"w-10 h-10 rounded-full\$1 overflow-hidden shrink-0\">
                                    @if(\$user->avatar)
                                        <img src=\"{{ asset('storage/' . \$user->avatar) }}\" class=\"w-full h-full object-cover\">
                                    @else
                                        {{ mb_substr(\$user->name, 0, 1) }}
                                    @endif
                                </div>",
    $content
);

// Add multipart form data
$content = str_replace(
    "<form :action=\"formAction\" method=\"POST\">",
    "<form :action=\"formAction\" method=\"POST\" enctype=\"multipart/form-data\">",
    $content
);

// Add Avatar Upload to modal
$avatarUpload = <<<HTML
<div class="flex flex-col sm:flex-row items-center gap-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="relative w-24 h-24 rounded-full bg-gray-50 border-4 border-white shadow-md flex items-center justify-center overflow-hidden group shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    <i class="fa-solid fa-camera text-gray-300 text-3xl"></i>
                                </template>
                                <label class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer text-white">
                                    <span class="text-[10px] font-bold">????? ??????</span>
                                    <input type="file" name="avatar" class="hidden" accept="image/*" @change="updatePhotoPreview">
                                </label>
                            </div>
                            <div class="text-center sm:text-right">
                                <h4 class="font-bold text-gray-800 text-base mb-1">?????? ???????</h4>
                                <p class="text-xs text-gray-500 mb-3">?????? ????? ?? ???? ?????? ??????? ?? ??? ?????.</p>
                            </div>
                        </div>
HTML;

$content = preg_replace("/<div class=\"bg-white p-6 rounded-2xl shadow-sm border border-gray-100\">.*?<h4 class=\"font-black text-gray-700/s", $avatarUpload . "\n\n                        <div class=\"bg-white p-6 rounded-2xl shadow-sm border border-gray-100\">\n                            <h4 class=\"font-black text-gray-700", $content, 1);

file_put_contents("resources/views/users/index.blade.php", $content);

