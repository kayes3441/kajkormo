<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\Pages;

use App\Filament\Clusters\Admin\CategoryManagement\SubCategoryResource\SubCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubCategory extends CreateRecord
{
    protected static string $resource = SubCategoryResource::class;
}
