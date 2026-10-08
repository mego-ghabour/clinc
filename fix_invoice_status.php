<?php
// 1. Fix InvoiceController store method
$f = "c:/Users/fkrtk/Desktop/Clinc/app/Http/Controllers/InvoiceController.php";
$c = file_get_contents($f);
$c = str_replace("'status' => 'غير مدفوعة',", "'status' => 'unpaid',", $c);
file_put_contents($f, $c);

// 2. Fix invoices index view
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/invoices/index.blade.php";
$c = file_get_contents($f);
$c = str_replace("== 'مدفوعة'", "=== 'paid'", $c);
$c = str_replace("=== 'مدفوعة'", "=== 'paid'", $c);
$c = str_replace("== 'مدفوعة جزئياً'", "=== 'partially_paid'", $c);
$c = str_replace("=== 'مدفوعة جزئياً'", "=== 'partially_paid'", $c);
$c = str_replace("== 'غير مدفوعة'", "=== 'unpaid'", $c);
$c = str_replace("=== 'غير مدفوعة'", "=== 'unpaid'", $c);
file_put_contents($f, $c);

// 3. Fix invoices show view
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/invoices/show.blade.php";
$c = file_get_contents($f);
$c = str_replace("== 'مدفوعة'", "=== 'paid'", $c);
$c = str_replace("=== 'مدفوعة'", "=== 'paid'", $c);
$c = str_replace("== 'مدفوعة جزئياً'", "=== 'partially_paid'", $c);
$c = str_replace("=== 'مدفوعة جزئياً'", "=== 'partially_paid'", $c);
$c = str_replace("== 'غير مدفوعة'", "=== 'unpaid'", $c);
$c = str_replace("=== 'غير مدفوعة'", "=== 'unpaid'", $c);
file_put_contents($f, $c);

// Update existing database rows
\App\Models\Invoice::where('status', 'غير مدفوعة')->update(['status' => 'unpaid']);
\App\Models\Invoice::where('status', 'مدفوعة')->update(['status' => 'paid']);
\App\Models\Invoice::where('status', 'مدفوعة جزئياً')->update(['status' => 'partially_paid']);

echo "Fixed Invoice Status logic.\n";
