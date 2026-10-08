<?php
$content = file_get_contents("routes/web.php");
$content = preg_replace(
    "~Route::get\('/dashboard', function \(\) \{.*?\n.*?\n.*?\}\)->name\('dashboard'\);~s",
    "Route::get('/dashboard', function () {\n        return view('dashboard');\n    })->name('dashboard');",
    $content
);
file_put_contents("routes/web.php", $content);

