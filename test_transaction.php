<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = App\Models\Customer::first();
$a = App\Models\Account::first();
$request = new \Illuminate\Http\Request();
$request->merge(['amount' => 10, 'date' => '2026-04-26', 'payment_type' => 'CASH', 'account_id' => $a->id]);
app('App\Http\Controllers\Admin\Invoice\InvoicePaymentController')->storeCustomerPayment($request, $c);
dump(App\Models\Transaction::latest()->first()->toArray());
