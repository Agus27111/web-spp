<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;

class PaymentController extends Controller
{
 public function print(Payment $payment)
{
    $pdf = Pdf::loadView('prints.payment', [
        'record' => $payment
    ]);

    return $pdf->stream('struk_pembayaran.pdf'); 
}
}
