<?php
$content = file_get_contents("resources/views/layouts/app.blade.php");

$profile = <<<HTML
        <!-- User Profile -->
        <div class="p-4 border-t border-white/10 flex items-center gap-3 hover:bg-white/5 cursor-pointer transition overflow-hidden">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-teal-500">
                @if(auth()->check() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ auth()->check() ? mb_substr(auth()->user()->name, 0, 1) : '?' }}
                @endif
            </div>
            <div class="truncate">
                <h3 class="font-bold text-sm truncate">{{ auth()->check() ? auth()->user()->name : '???? ??????' }}</h3>
                <p class="text-xs text-gray-300 truncate">{{ auth()->check() && auth()->user()->roles->count() ? auth()->user()->roles->first()->name : 'SUPER ADMIN' }}</p>
            </div>
        </div>
HTML;

$content = preg_replace("/<!-- User Profile -->.*?<\/aside>/s", $profile . "\n    </aside>", $content);

file_put_contents("resources/views/layouts/app.blade.php", $content);

