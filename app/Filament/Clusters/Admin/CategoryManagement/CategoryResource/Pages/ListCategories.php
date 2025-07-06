<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\Pages;

use App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
