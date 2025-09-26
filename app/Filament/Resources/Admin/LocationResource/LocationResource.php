<?php

namespace App\Filament\Resources\Admin\LocationResource;;

use App\Filament\Resources\Admin\LocationResource\Pages\CreateLocation;
use App\Filament\Resources\Admin\LocationResource\Pages\EditLocation;
use App\Filament\Resources\Admin\LocationResource\Pages\ListLocations;
use App\Filament\Resources\Admin\LocationResource\RelationManagers\ChildrenRelationManager;
use App\Models\Country;
use App\Models\Language;
use App\Models\Location;
use Filament\Forms\{Components\Grid,
    Components\Hidden,
    Components\Repeater,
    Components\Section,
    Components\Select,
    Components\TextInput,
    Get,
    Form};
use Filament\Tables\{Actions\Action,
    Actions\DeleteAction,
    Actions\DeleteBulkAction,
    Actions\EditAction,
    Columns\TextColumn,
    Table};
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class LocationResource extends Resource
{
    protected static ?string $model            = Location::class;
    protected static ?string $navigationIcon   = 'heroicon-s-map-pin';
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
            Repeater::make('translations')
                ->relationship()
                ->schema([
                    Hidden::make('key')->default('name'),
                    Select::make('locale')
                        ->options(fn () => Language::active()->withoutEN()->pluck('name', 'code')->toArray())
                        ->reactive()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->required(),

                    TextInput::make('value')
                        ->label('Translation')
                        ->required(),
                ])
                ->columns(2)
                ->label('Translations')
                ->addActionLabel('Add Translation')
                ->maxItems(Language::active()->withoutEN()->count()),
            Select::make('country_code')
                ->label('Country')
                ->options(fn () => Country::query()
                    ->where('is_active', true)
                    ->pluck('name', 'iso2'))
                ->searchable()
                ->required()
                ->reactive(),

            Select::make('level')
                ->label('Level')
                ->options(function (Get $get) {
                    $code = $get('country_code');
                    if (!$code) return [];
                    $labels = Country::where('iso2', $code)->value('location_labels');
                    return collect(json_decode($labels, true) ?? [])
                        ->mapWithKeys(fn($item) => [$item => ucfirst(str_replace('_', ' ', $item))])
                        ->toArray();
                })
                ->live()
                ->required(),

            Select::make('parent_id')
                ->label('Parent Location')
                ->relationship(
                    name: 'parent',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        $level = $get('level');
                        $country = $get('country_code');

                        if (!$level || !$country) {
                            return $query->whereNull('id');
                        }
                        $query->where('country_code', $country);

                        return match ($level) {
                            'metropolitan'   => $query->where('level', 'division'),
                            'district'       => $query->whereIn('level', ['division', 'metropolitan']),
                            'sub-district'   => $query->where('level', 'district'),
                            default          => $query->whereNull('id'),
                        };
                    }
                )
                ->visible(fn (Get $get) => $get('level') !== 'division')
                ->required(fn (Get $get) => $get('level') !== 'division')
                ->searchable()
                ->reactive(),

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
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_','-',$state)))
                    ->color(fn (string $state) => match ($state) {
                        'division'     => 'success',
                        'metropolitan' => 'info',
                        'district'     => 'warning',
                        'sub-district'  => 'gray',
                    }),

                TextColumn::make('parent.name')
                    ->label('Parent Location')
            ])
            ->actions([
                Action::make('toggleStatus')
                    ->label(fn ($record) => $record->status === 1 ? 'Disable' : 'Enable')
                    ->icon(fn ($record) => $record->status === 1 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->status === 1 ? 'danger' : 'success')
                    ->action(fn ($record) => $record->update([
                        'status' => $record->status === 1 ? 0 : 1,
                    ]))
                    ->after(function ($record) {
                        Notification::make()
                            ->title('Status updated successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !is_null($record->status)),
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
