<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function bookDetail()
    {
        return view('app.book-detail');
    }

    public function bookContact()
    {
        return view('app.book-contact');
    }

    public function bookPayment()
    {
        return view('app.book-payment');
    }

    public function midtransNotification()
    {
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        $verify_signature_key = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key'));

        if ($verify_signature_key == $notif->signature_key) {
            if ($transaction == 'settlement') {
                $transaction = Transaction::where('order_id', $notif->order_id)->firstOrFail();
                $ticket = Ticket::where('transaction_id', $transaction->id)->get();
                foreach ($ticket as $ticket) {
                    $ticket->status = "Active";
                    $ticket->save();
                }
                $transaction->status = "Paid";
                $transaction->save();
            } else if ($transaction == 'cancel') {
                if ($fraud == 'challenge') {
                    // TODO Set payment status in merchant's database to 'failure'
                } else if ($fraud == 'accept') {
                    // TODO Set payment status in merchant's database to 'failure'
                }
            } else if ($transaction == 'deny') {
                // TODO Set payment status in merchant's database to 'failure'
            }
        }
    }
}
