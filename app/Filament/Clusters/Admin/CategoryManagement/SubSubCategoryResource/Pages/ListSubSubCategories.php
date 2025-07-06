<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource\Pages;

use App\Filament\Clusters\Admin\CategoryManagement\SubSubCategoryResource\SubSubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubSubCategories extends ListRecords
{
    protected static string $resource = SubSubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
