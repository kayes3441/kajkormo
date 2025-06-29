<?php

namespace App\Filament\Resources\Admin\EmployeeResource\Pages;

use App\Filament\Resources\Admin\EmployeeResource\EmployeeResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

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
    protected function getSavedNotificationTitle():string
    {
        return 'Employee information successfully updated.';
    }
}
