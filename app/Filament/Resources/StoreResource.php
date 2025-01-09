<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\RajaOngkirSetting as ModelsRajaOngkirSetting;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Services\RajaOngkirService;
use App\Models\RajaOngkirSetting;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;


class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        $rajaongkir = new RajaOngkirService();
        $rajaongkirSetting = RajaOngkirSetting::getActive();
        $isProVersion = $rajaongkirSetting?->isPro() ?? false;

        if (!$rajaongkirSetting?->is_valid) {
            Notification::make()
                ->title('Rajaongkir API is not valid')
                ->body('Please configure valid Rajaongkir settings before creating a store')
                ->danger()
                ->send();

            return $form->schema([]);
        }


        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('banner')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp')
                    ->maxLength(255),
                Forms\Components\Select::make('province_id') // Use Select instead of TextInput
                    ->label('Province')
                    ->options(fn() => $rajaongkir->getProvinces())
                    ->default(fn($record) => $record?->province_id)
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) use ($rajaongkir) {
                        $set('regency_id', null);
                        $set('regency_name', null);

                        if ($state) {
                            $provinces = $rajaongkir->getProvinces();
                            $set('province_name', $provinces[$state] ?? '');
                        }
                    })
                    ->required(),
                Forms\Components\TextInput::make('subdistrict_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('province_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('regency_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subdistrict_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subdistrict_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regency_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subdistrict_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Store::count() < 1;
    }
}
