<?php

namespace App\Filament\Resources\Admin\NotificationResource;

use App\Models\Language;
use App\Models\NotificationMessage;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NotificationResource extends Resource
{
    protected static ?string $model = NotificationMessage::class;
    protected static ?string $label = 'Notifications';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Textarea::make('message')
                        ->required()
                        ->maxLength(100),
                    Repeater::make('translations')
                        ->relationship()
                        ->schema([
                            Hidden::make('key')->default('message'),
                            Select::make('locale')
                                ->options(fn () => Language::active()->withoutEN()->pluck('name', 'code')->toArray())
                                ->reactive()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required()->columnSpanFull(),

                            Textarea::make('value')
                                ->label('Translation')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->label('Translations')
                        ->addActionLabel('Add Translation')
                        ->maxItems(Language::active()->withoutEN()->count()),
                ])
            ]);
    }
    public static function getSlug(): string
    {
        return 'notification';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->hidden(),
                TextColumn::make('key')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state) => Str::of($state)->headline()),

                TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    protected static bool $canCreate = false;
    public static function canCreate(): bool
    {
        return false;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
