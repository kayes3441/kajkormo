<?php

namespace App\Filament\Resources\Admin\LanguageResource;

use App\Models\Country;
use App\Models\Language;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static? string $navigationGroup = 'System Configuration';
    public static function getSlug(): string
    {
        return 'language';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create Language')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->required(),
                        Select::make('code')
                            ->label('Language Code')
                            ->options(function () {
                                return Country::select('language_code')->active()->get()
                                    ->mapWithKeys(fn ($country) => [
                                        $country->language_code => strtolower($country->language_code),
                                    ])->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                        Select::make('direction')
                            ->options([
                                'ltr' => 'Left to Right',
                                'rtl' => 'Right to Left',
                            ])
                            ->default('ltr')
                            ->required(),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('status')
                            ->label('Is Active')
                            ->helperText('Language status.'),
                        Toggle::make('default_status')
                            ->label('Is Default')
                            ->helperText('Only one language can be default.'),
                    ])

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->hidden(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('code'),
                ToggleColumn::make('status')
                    ->label('Active')
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update(['status' => $state]);

                        Notification::make()
                            ->title('Status Updated')
                            ->body('The active status was set to '.($state ? 'On' : 'Off'))
                            ->success()
                            ->send();
                    }),

                ToggleColumn::make('default_status')
                    ->label('Default')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            // Turn off all other default_status first
                            $record->newQuery()
                                ->where('id', '!=', $record->id)
                                ->update(['default_status' => 0]);
                        }

                        $record->update(['default_status' => $state]);

                        Notification::make()
                            ->title('Default Status Updated')
                            ->body($state
                                ? 'This record is now the default, all others were unset.'
                                : 'This record is no longer the default.')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make()->hidden(function ($record) {
                    return !($record->code != 'EN');
                }),
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
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
