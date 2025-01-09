<?php

namespace App\Filament\Resources\RajaOngkirSettingResource\Pages;

use App\Filament\Resources\RajaOngkirSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRajaOngkirSetting extends EditRecord
{
    protected static string $resource = RajaOngkirSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
