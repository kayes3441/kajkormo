<?php

namespace App\Filament\Resources\Admin\LocationResource\Pages;

use App\Filament\Resources\Admin\LocationResource\LocationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Location successfully created.';
    }
}
