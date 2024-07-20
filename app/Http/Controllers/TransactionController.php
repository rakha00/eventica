<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function midtransNotification()
    {
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $verifySignatureKey = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key'));

        if ($verifySignatureKey === $notif->signature_key) {
            $transaction = Transaction::where('order_id', $notif->order_id)->firstOrFail();
            if ($transactionStatus === 'settlement') {
                $tickets = Ticket::where('transaction_id', $transaction->id)->get();
                foreach ($tickets as $ticket) {
                    $ticket->status = "Active";
                    $ticket->save();
                }
                $transaction->status = "Completed";
                $transaction->save();
            } elseif ($transactionStatus === 'pending') {
                // TODO Set payment status in merchant's database to 'pending'
            } elseif ($transactionStatus === 'cancel') {
                // TODO Set payment status in merchant's database to 'canceled'
            } elseif ($transactionStatus === 'expire') {
                // TODO Set payment status in merchant's database to 'expire'
            }
        }
    }
}
