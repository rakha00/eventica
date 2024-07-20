<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Transaction;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('User count', User::count())
                ->description('Total users')
                ->descriptionIcon('heroicon-m-user-group'),
            Stat::make('Tickets Sold', Ticket::where('status', 'Active')->count())
                ->description('Total tickets sold')
                ->descriptionIcon('heroicon-m-ticket'),
            Stat::make('Transaction count', Transaction::where('status', 'completed')->count())
                ->description('Total transactions completed')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Total Profit', number_format(Transaction::where('status', 'completed')->sum('total_price'), 0, ',', '.'))
                ->description('Total profit')
                ->descriptionIcon('heroicon-m-currency-dollar'),
        ];
    }
}
