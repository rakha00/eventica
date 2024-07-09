<?php

namespace App\Http\Controllers;

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
}
