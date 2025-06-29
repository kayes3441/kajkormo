<?php

namespace App\Filament\Resources\Admin\EmployeeResource\Pages;

use App\Filament\Resources\Admin\EmployeeResource\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Employee information successfully created.';
    }
}
