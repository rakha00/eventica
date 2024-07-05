<?php

namespace App\Filament\Resources\EventPackageResource\Pages;

use App\Filament\Resources\EventPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventPackages extends ManageRecords
{
    protected static string $resource = EventPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
