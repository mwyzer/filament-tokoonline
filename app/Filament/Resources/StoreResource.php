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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Basic Information')
                            ->description('Basic Information of your online shop')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('whatsapp')
                                    ->prefix('+62')
                                    ->helperText('Mohon masukan nomor tanpa angka 0, di awal. Contoh 81234567890 ')
                                    ->placeholder('81234567890')
                                    ->required()
                                    ->numeric()
                                    ->dehydrateStateUsing(fn($state) => '62' . ltrim($state, '62'))
                                    ->formatStateUsing(fn($state) => ltrim($state, '62'))
                                    ->validationAttribute('Nomor WhatsApp')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('image')
                                    ->image()
                                    ->directory('stores')
                                    ->required(),
                                Forms\Components\FileUpload::make('banner')
                                    ->image()
                                    ->directory('stores/banner')
                                    ->required(),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Address')
                            ->description('Use RajaOngkir API')
                            ->schema([
                                Forms\Components\Select::make('province_id')
                                    ->label('Province')
                                    ->options(fn() => $rajaongkir->getProvinces())
                                    ->default(function ($record) {
                                        return $record?->province_id;
                                    })
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) use ($rajaongkir) {
                                        $set('regency_id', null);
                                        $set('regency_name', null);

                                        if ($state) {
                                            $provinces = $rajaongkir->getProvinces();
                                            $set('province_name', $provinces[$state] ?? '');
                                        }
                                    })
                                    ->required(),
                                Forms\Components\Select::make('regency_id')
                                    ->label('Regency')
                                    ->required()
                                    ->options(function (Get $get, $record) use ($rajaongkir) {
                                        $provinceId = $get('province_id') ?? $record?->province_id;
                                        if (!$provinceId) {
                                            return [];
                                        }

                                        return $rajaongkir->getCities($provinceId);
                                    })
                                    ->default(function ($record) {
                                        return $record?->regency_id;
                                    })
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) use ($rajaongkir) {
                                        if ($state) {
                                            $cities = $rajaongkir->getCities($get('province_id'));
                                            $set('regency_name', $cities[$state] ?? '');
                                        }
                                    }),
                                // Forms\Components\TextInput::make('subdistrict_id')
                                //     ->numeric(),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('province_name'),
                                Forms\Components\TextInput::make('regency_name'),
                                // Forms\Components\TextInput::make('subdistrict_name'),
                            ]),

                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regency_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province_name')
                    ->numeric()
                    ->sortable(),
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
