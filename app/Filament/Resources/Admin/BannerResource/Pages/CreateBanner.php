<?php

namespace App\Filament\Resources\Admin\BannerResource\Pages;

use App\Filament\Resources\Admin\BannerResource\BannerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBanner extends CreateRecord
{
    protected static string $resource = BannerResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Banner successfully created.';
    }
}
