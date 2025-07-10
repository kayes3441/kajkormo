<?php

namespace App\Filament\Resources\Admin\LocationResource;;

use App\Filament\Resources\Admin\LocationResource\Pages\CreateLocation;
use App\Filament\Resources\Admin\LocationResource\Pages\EditLocation;
use App\Filament\Resources\Admin\LocationResource\Pages\ListLocations;
use App\Filament\Resources\Admin\LocationResource\RelationManagers\ChildrenRelationManager;
use App\Models\Location;
use Filament\Forms\{Components\Grid, Components\Section, Components\Select, Components\TextInput, Get, Form};
use Filament\Tables\{Actions\DeleteAction,
    Actions\DeleteBulkAction,
    Actions\EditAction,
    Columns\TextColumn,
    Table};
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class LocationResource extends Resource
{
    protected static ?string $model            = Location::class;
    protected static ?string $navigationIcon   = 'heroicon-o-map';
    protected static ?string $navigationGroup  = 'Administrative Areas';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                    Grid::make(2)->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(100),

            Select::make('level')
                ->options([
                    'division'     => 'Division',
                    'metropolitan' => 'Metropolitan',
                    'district'     => 'District',
                    'sub_district'  => 'Sub‑district',
                ])
                ->live()
                ->required(),

            Select::make('parent_id')
                ->label('Parent Location')
                ->relationship(
                    name: 'parent',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query, Get $get) => match ($get('level')) {
                        'metropolitan' => $query->where('level', 'division'),
                        'district'     => $query->whereIn('level', ['division', 'metropolitan']),
                        'sub_district'  => $query->where('level', 'district'),
                        default        => $query->whereNull('parent_id'),
                    },
                )
                ->visible(fn (Get $get) => $get('level') !== 'division')
                ->required(fn (Get $get) => $get('level') !== 'division')
                ->searchable(),

            TextInput::make('latitude')
                ->numeric()
                ->step(0.0000001)
                ->maxValue(90)
                ->minValue(-90),

            TextInput::make('longitude')
                ->numeric()
                ->step(0.0000001)
                ->maxValue(180)
                ->minValue(-180),
                    ])
                ])
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('level')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'division'     => 'success',
                        'metropolitan' => 'info',
                        'district'     => 'warning',
                        'sub_district'  => 'gray',
                    }),

                TextColumn::make('parent.name')
                    ->label('Parent Location')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('level');
    }

    public static function getRelations(): array
    {
        return [
            ChildrenRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index'  => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit'   => EditLocation::route('/{record}/edit'),
        ];
    }
}
