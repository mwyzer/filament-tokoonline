<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Components\Group;
use Filament\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-cube';

    protected static ?string $navigationGroup = 'Products';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required(),
                                TextInput::make('name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Product::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->maxLength(255),
                                
                            ]),
                        Forms\Components\Section::make('Description')
                            ->schema([
                                RichEditor::make('description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('Images')
                            ->schema([
                                FileUpload::make('images')
                                ->columnSpanFull()
                                ->multiple()
                                ->directory('uploads/images') // Specify the directory for uploaded files
                                ->maxFiles(5) // Optional: Limit the number of files
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ]),
                        
                        
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Price & Stock')
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                                TextInput::make('stock')
                                    ->required()
                                    ->numeric(),
                                Toggle::make('is_active')
                                    ->required()
                                    ->helperText('Enable or disable product visibility')
                                    ->default(false)
                                    ->label('Active'),
                            ]),
                        Forms\Components\Section::make('Product Dimensions')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        TextInput::make('weight')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->label('Weight (gram)'),
                                        TextInput::make('height')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->label('Height (cm)'),
                                        TextInput::make('width')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->label('Width (cm)'),
                                        TextInput::make('length')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->label('Length (cm)'),
                                    ])
                                
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->circular()
                    ->stacked(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active') // Use ToggleColumn for editing
                    ->label('Active'), // Optional label
                    
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
