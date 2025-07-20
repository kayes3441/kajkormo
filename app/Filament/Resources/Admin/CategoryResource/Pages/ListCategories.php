<?php

namespace App\Filament\Resources\Admin\CategoryResource\Pages;

use App\Filament\Resources\Admin\CategoryResource\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'Category' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'category'))
                ->badge(Category::where('level', 'category')->count()),

            'Subcategory' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'subcategory'))
                ->badge(Category::where('level', 'subcategory')->count()),

            'Sub-Subcategory' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'sub-subcategory'))
                ->badge(Category::where('level', 'sub-subcategory')->count()),
        ];
    }
}
