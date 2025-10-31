<?php

namespace App\Filament\Resources\Admin\NotificationTopicResource\Pages;

use App\Filament\Resources\Admin\NotificationTopicResource\NotificationTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationTopics extends ListRecords
{
    protected static string $resource = NotificationTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
