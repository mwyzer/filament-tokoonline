<?php

namespace App\Filament\Resources\RajaOngkirSettingResource\Pages;

use App\Filament\Resources\RajaOngkirSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRajaOngkirSetting extends EditRecord
{
    protected static string $resource = RajaOngkirSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $isValid = $this->record->validateApiKey();

        if ($isValid) {
            Notification::make()
                ->title('API Key is valid !!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('API Key is invalid !!')
                ->danger()
                ->body($record->error_message ?? 'Unknown error')
                ->send();
        }
    }
}
