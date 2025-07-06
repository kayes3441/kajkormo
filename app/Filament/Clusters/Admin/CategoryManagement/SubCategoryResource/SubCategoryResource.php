<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource;

use App\Filament\Clusters\Admin\CategoryManagement\CategoryManagement;
use App\Models\Category as SubCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Subcategory';

    protected static ?string $cluster = CategoryManagement::class;

    public static function getSlug(): string
    {
        return 'subcategory';
    }


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
            'index' => \App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\Pages\ListSubCategories::route('/'),
            'create' => \App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\Pages\CreateSubCategory::route('/create'),
            'edit' => \App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
