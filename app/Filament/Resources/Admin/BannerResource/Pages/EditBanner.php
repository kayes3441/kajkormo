<?php

namespace App\Filament\Resources\Admin\BannerResource\Pages;

use App\Filament\Resources\Admin\BannerResource\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
