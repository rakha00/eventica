<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Transaction;

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
                    $ticket->update(['status' => "Active"]);
                }
                $transaction->update(['status' => 'Completed']);
            } elseif ($transactionStatus === 'pending') {
                $transaction->update(['status' => 'On payment']);
            } elseif ($transactionStatus === 'cancel') {
                Ticket::where('transaction_id', $transaction->id)->delete();
                $transaction->eventPackage->increment('remaining', $transaction->quantity);
                $transaction->update(['status' => "Canceled"]);
            } elseif ($transactionStatus === 'expire') {
                Ticket::where('transaction_id', $transaction->id)->delete();
                $transaction->eventPackage->increment('remaining', $transaction->quantity);
                $transaction->update(['status' => "Expired"]);
            }
        }
    }
}
