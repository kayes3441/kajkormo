<?php

namespace App\Filament\Resources\Admin\CategoryResource\Pages;

use App\Filament\Resources\Admin\CategoryResource\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Category successfully created.';
    }
}
