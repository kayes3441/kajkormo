<?php

namespace App\Filament\Resources\Admin\AdminRoleResource\Pages;

use App\Filament\Resources\Admin\AdminRoleResource\AdminRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminRole extends EditRecord
{
    protected static string $resource = AdminRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Employee Role successfully created.';
    }
}
