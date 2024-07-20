<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesReportChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Report';

    protected static ?int $sort = 1;

    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        switch ($activeFilter) {
            case 'week':
                $data = $this->getWeeklyData();
                break;
            case 'month':
                $data = $this->getMonthlyData();
                break;
            case 'year':
                $data = $this->getYearlyData();
                break;
            default:
                $data = $this->getWeeklyData();
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }


    protected function getWeeklyData()
    {
        return Trend::model(Transaction::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();
    }

    protected function getMonthlyData()
    {
        return Trend::model(Transaction::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();
    }

    protected function getYearlyData()
    {
        return Trend::model(Transaction::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
