<?php
// Fix Index
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/invoices/index.blade.php";
$c = file_get_contents($f);
$c = str_replace('$invoice->total_amount', '$invoice->total', $c);
$c = str_replace('$invoice->due_amount', '$invoice->balance', $c);
file_put_contents($f, $c);

// Fix Show
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/invoices/show.blade.php";
$c = file_get_contents($f);
$c = str_replace('$invoice->total_amount', '$invoice->total', $c);
$c = str_replace('$invoice->due_amount', '$invoice->balance', $c);
$c = str_replace('$invoice->sub_total', '$invoice->subtotal', $c);
$c = str_replace('$invoice->tax_amount', '$invoice->tax', $c);
file_put_contents($f, $c);

// Fix PaymentController store method validation for max amount
$f = "c:/Users/fkrtk/Desktop/Clinc/app/Http/Controllers/PaymentController.php";
if(file_exists($f)) {
    $c = file_get_contents($f);
    $c = str_replace('$invoice->due_amount', '$invoice->balance', $c);
    file_put_contents($f, $c);
}
echo "Fixed properties.\n";
