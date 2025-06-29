<?php

namespace App\Filament\Resources\Admin\AdminRoleResource\Pages;

use App\Filament\Resources\Admin\AdminRoleResource\AdminRoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminRole extends CreateRecord
{
    protected static string $resource = AdminRoleResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Employee Role successfully created.';
    }
}
