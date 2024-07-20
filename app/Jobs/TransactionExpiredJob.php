<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use App\Events\TransactionExpiredEvent;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransactionExpiredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (in_array($this->transaction->status, ['Pending', 'On payment'])) {
            Ticket::where('transaction_id', $this->transaction->id)->delete();
            $this->transaction->eventPackage->increment('remaining', $this->transaction->quantity);
            $this->transaction->update(['status' => 'Expired']);
        }
    }
}
