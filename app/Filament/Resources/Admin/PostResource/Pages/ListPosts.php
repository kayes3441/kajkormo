<?php

namespace App\Filament\Resources\Admin\PostResource\Pages;

use App\Filament\Resources\Admin\PostResource\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
