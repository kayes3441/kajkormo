<?php

namespace App\Filament\Resources\Admin\AdminRoleResource\Pages;

use App\Filament\Resources\Admin\AdminRoleResource\AdminRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminRoles extends ListRecords
{
    protected static string $resource = AdminRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
