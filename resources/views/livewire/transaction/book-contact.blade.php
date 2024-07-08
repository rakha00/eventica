<?php
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public $transaction;
    public $tickets = [];
    public $ticketIds = [];
    public $isFilled = false;

    public function mount(Transaction $transaction)
    {
        $this->transaction = Transaction::where("order_id", request()->route("orderId"))->where("status", "pending")->first();
        if ($this->transaction) {
            $tickets = Ticket::where("transaction_id", $this->transaction->id)->get();
            foreach ($tickets as $index => $ticket) {
                if ($index == 0) {
                    $this->tickets[] = [
                        "name" => $this->transaction->user->name,
                        "phone" => $this->transaction->user->phone,
                        "email" => $this->transaction->user->email,
                        "identity_card_number" => $ticket->identity_card_number,
                    ];
                } else {
                    $this->tickets[] = [
                        "name" => $ticket->name,
                        "phone" => $ticket->phone,
                        "email" => $ticket->email,
                        "identity_card_number" => $ticket->identity_card_number,
                    ];
                }
                $this->ticketIds[] = $ticket->id;
            }
        } else {
            abort(404);
        }
    }

    public function updateUser()
    {
        User::where("id", $this->transaction->user_id)->update([
            "phone" => $this->tickets[0]["phone"],
            "identity_card_number" => $this->tickets[0]["identity_card_number"],
        ]);
    }

    public function updateTicket()
    {
        foreach ($this->tickets as $index => $ticket) {
            Ticket::where("id", $this->ticketIds[$index])->update([
                "name" => $ticket["name"],
                "phone" => $ticket["phone"],
                "email" => $ticket["email"],
                "identity_card_number" => $ticket["identity_card_number"],
            ]);
        }
    }

    public function updateTransaction()
    {
        Transaction::where("id", $this->transaction->id)->update([
            "status" => "on payment",
        ]);
    }

    public function fillAllInformation()
    {
        if ($this->isFilled) {
            foreach ($this->tickets as $index => $ticket) {
                if ($index != 0) {
                    $this->tickets[$index]["phone"] = "";
                    $this->tickets[$index]["email"] = "";
                }
            }
            $this->isFilled = false;
        } else {
            foreach ($this->tickets as $index => $ticket) {
                if ($index != 0) {
                    $this->tickets[$index]["phone"] = $this->tickets[0]["phone"];
                    $this->tickets[$index]["email"] = $this->tickets[0]["email"];
                }
            }
            $this->isFilled = true;
        }
    }

    public function bookContact()
    {
        $this->updateUser();
        $this->updateTicket();
        $this->updateTransaction();
        return redirect()->route("book-payment", $this->transaction->order_id);
    }
};
?>

<div>
    @if (session()->has("error"))
        <div class="mt-4 rounded-md bg-red-500 p-4 text-white">
            {{ session("error") }}
        </div>
    @endif
    <h2 class="mb-2 text-2xl font-semibold text-white">Visitor Details</h2>
    <p class="text-xs text-gray-400">Make sure to fill in the visitor details correctly for a smooth experience.
        First ticket information will be used for the transaction details and we will update your user information.</p>
    <!-- Visitor -->
    @foreach ($tickets as $index => $ticket)
        <div class="mt-4 space-y-4 rounded-md bg-gray-800 p-4">
            <h3 class="text-md font-semibold text-white sm:text-lg">Ticket {{ $index + 1 }} (Pax)</h3>
            @if ($index == 0 && count($tickets) > 1)
                <label class="inline-flex cursor-pointer flex-col items-start sm:flex-row">
                    <input class="peer sr-only" type="checkbox">
                    <div class="peer relative mb-2 me-2 h-4 w-7 rounded-full border-gray-600 bg-gray-700 after:absolute after:start-[2px] after:top-[2px] after:h-3 after:w-3 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-tertiary/80 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-tertiary rtl:peer-checked:after:-translate-x-full"
                        wire:click="fillAllInformation">
                    </div>
                    <span class="text-sm font-medium text-white">Fill all information with same phone number and email</span>
                </label>
            @endif
            <input class="w-full rounded-md bg-gray-700 p-2 text-white" type="text" wire:model="tickets.{{ $index }}.name" placeholder="Name"
                @if ($index == 0) value="{{ $transaction->user->name }}" @endif>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg id="indonesia-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 20" width="20" height="20">
                        <rect width="30" height="20" fill="#fff" />
                        <rect width="30" height="10" fill="#ce1126" />
                    </svg>
                </div>
                <input class="block w-full rounded-md bg-gray-700 p-2 ps-10 text-white" type="text" wire:model="tickets.{{ $index }}.phone" placeholder="Phone Number"
                    @if ($index == 0) value="{{ $transaction->user->phone }}" @endif onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="15" />
            </div>
            <input class="w-full rounded-md bg-gray-700 p-2 text-white" type="email" wire:model="tickets.{{ $index }}.email" placeholder="Email"
                @if ($index == 0) value="{{ $transaction->user->email }}" readonly @endif>
            <input class="w-full rounded-md bg-gray-700 p-2 text-white" type="text" wire:model="tickets.{{ $index }}.identity_card_number" placeholder="Identity Card Number"
                @if ($index == 0) value="{{ $transaction->user->identity_card_number }}" @endif onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="16">
        </div>
    @endforeach

    <div class="mt-4 rounded-md bg-gray-800 p-4">
        <!-- Total Payment -->
        <div class="mb-2 inline-flex w-full justify-between border-b-2 border-dashed">
            <h3 class="text-md mb-2 font-semibold text-white sm:text-lg">Total Payment</h3>
            <p class="text-md font-bold text-tertiary">
                {{ "Rp " . number_format($transaction->total_price, 0, ",", ".") }}
            </p>
        </div>
    </div>
    <form wire:submit="bookContact">
        <button class="mt-4 w-full rounded-md bg-tertiary px-4 py-2 font-bold text-black hover:bg-tertiary/80">
            Next
        </button>
    </form>
</div>
