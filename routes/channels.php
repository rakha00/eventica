<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('transaction.{transactionId}', function (User $user, int $transactionId) {
    return $user->id === Transaction::findOrNew($transactionId)->user_id;
});
