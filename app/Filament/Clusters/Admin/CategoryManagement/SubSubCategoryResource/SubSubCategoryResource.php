<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource;

use App\Filament\Clusters\Admin\CategoryManagement\CategoryManagement;
use App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource\Pages\CreateSubSubCategory;
use App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource\Pages\EditSubSubCategory;
use App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource\Pages\ListSubSubCategories;
use App\Models\Category as SubSubCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubSubCategoryResource extends Resource
{
    protected static ?string $model = SubSubCategory::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Sub-Subcategory';

    protected static ?string $cluster = CategoryManagement::class;
    public static function getSlug(): string
    {
        return 'sub-subcategory';
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
            'index' => ListSubSubCategories::route('/'),
            'create' => CreateSubSubCategory::route('/create'),
            'edit' => EditSubSubCategory::route('/{record}/edit'),
        ];
    }
}
