<?php

namespace App\Filament\Resources\RajaOngkirSettingResource\Pages;

use App\Filament\Resources\RajaOngkirSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRajaOngkirSettings extends ListRecords
{
    protected static string $resource = RajaOngkirSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
