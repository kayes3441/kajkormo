<?php

namespace App\Filament\Resources\Admin\EmployeeResource\Pages;

use App\Filament\Resources\Admin\EmployeeResource\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
