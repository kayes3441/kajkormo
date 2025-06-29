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
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                            ->label('Country')
                            ->options(function () {
                                return Country::select('flag','code')->active()->get()
                                    ->mapWithKeys(fn ($country) => [
                                        $country->code => $country->code,
                                    ])->toArray();
                            })
                            ->searchable()
                            ->required(),
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
                IconColumn::make('default_status')
                    ->boolean()
                    ->label('Default'),

                IconColumn::make('status')
                    ->boolean()
                    ->label('Active'),
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
