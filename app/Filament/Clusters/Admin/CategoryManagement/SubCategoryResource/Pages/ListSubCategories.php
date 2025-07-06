<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\Pages;

use App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubCategories extends ListRecords
{
    protected static string $resource = SubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
