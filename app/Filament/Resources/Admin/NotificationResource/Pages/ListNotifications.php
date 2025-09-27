<?php

namespace App\Filament\Resources\Admin\NotificationResource\Pages;

use App\Filament\Resources\Admin\NotificationResource\NotificationResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
