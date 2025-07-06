<?php

namespace App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\Pages;

use App\Filament\Clusters\Admin\CategoryManagement\CategoryResource\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
