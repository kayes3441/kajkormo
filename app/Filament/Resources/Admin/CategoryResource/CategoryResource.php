<?php

namespace App\Filament\Resources\Admin\CategoryResource;

use App\Filament\Resources\Admin\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\Admin\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\Admin\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\Admin\LocationResource\RelationManagers\ChildrenRelationManager;
use App\Models\Category;
use App\Models\Language;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static? string $navigationGroup = 'Promotion Management';

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
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
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('value')
                                    ->label('Translation'),
                            ])
                            ->columns(2)
                            ->label('Translations')
                            ->addActionLabel('Add Translation')
                            ->maxItems(Language::active()->withoutEN()->count()),

                        Select::make('level')
                            ->required()
                            ->options([
                                'category' => 'Category',
                                'subcategory' => 'Subcategory',
                                'sub-subcategory' => 'Sub-subcategory',
                            ])
                            ->reactive(),

                        Select::make('parent_id')
                            ->label('Parent')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    return match ($get('level')) {
                                        'subcategory' => $query->where('level', 'category'),
                                        'sub-subcategory' => $query->where('level', 'subcategory'),
                                        default => $query->whereNull('id'),
                                    };
                                }
                            )
                            ->visible(fn (Get $get) => in_array($get('level'), ['subcategory', 'sub-subcategory']))
                            ->required(fn (Get $get) => in_array($get('level'), ['subcategory', 'sub-subcategory']))
                            ->searchable(),

                        FileUpload::make('icon')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->label('Icon')
                            ->directory('category'),

                        Select::make('priority')
                            ->options(
                                collect(range(1, 20))->mapWithKeys(fn ($i) => [$i => $i])->toArray()
                            )
                            ->label('Priority'),

                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label('Icon')
                    ->height(40)
                    ->width(40)
                    ->circular(),
                TextColumn::make('name')->sortable()->searchable(),

                TextColumn::make('level')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('-', ' ', $state)))
                    ->color(fn (string $state) => match ($state) {
                        'category' => 'success',
                        'subcategory' => 'info',
                        'sub-subcategory' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('parent.name')->label('Parent')->sortable(),


            ])
            ->actions([
                Action::make('toggleStatus')
                    ->label(fn ($record) => $record->status ? 'Disable' : 'Enable')
                    ->icon(fn ($record) => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->status ? 'danger' : 'success')
                    ->action(fn ($record) => $record->update(['status' => !$record->status]))
                    ->after(function ($record) {
                        Notification::make()
                            ->title('Status updated successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('priority');
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
