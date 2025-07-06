<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\CategoryResource;

use App\Filament\Clusters\Admin\CategoryManagement\CategoryManagement;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CategoryManagement::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => \App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\Pages\ListCategories::route('/'),
            'create' => \App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\Pages\CreateCategory::route('/create'),
            'edit' => \App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
