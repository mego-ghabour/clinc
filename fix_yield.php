<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/layouts/app.blade.php";
$c = file_get_contents($f);
if (strpos($c, "@yield('scripts')") === false) {
    $c = str_replace(
        "</body>",
        "    @yield('scripts')\n</body>",
        $c
    );
    file_put_contents($f, $c);
}
echo "Injected scripts yield.\n";
