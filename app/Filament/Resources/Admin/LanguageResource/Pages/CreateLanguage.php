<?php

namespace App\Filament\Resources\Admin\LanguageResource\Pages;

use App\Filament\Resources\Admin\LanguageResource\LanguageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;
    protected function afterCreate(): void
    {
        $lang = $this->record->code;
        $langPath = resource_path("lang/{$lang}");

        if (!file_exists($langPath)) {
            mkdir($langPath, 0777, true);
        }
        $enMessages = file_get_contents(resource_path('lang/EN/messages.php')) ? : "<?php return [];";
        $enNewMessages = file_get_contents(resource_path('lang/EN/new-messages.php')) ?: "<?php return [];";

        file_put_contents("{$langPath}/messages.php", "<?php return [];");
        file_put_contents("{$langPath}/new-messages.php", $enMessages);

        // Merge both into new-messages.php
        $translated = include resource_path('lang/EN/messages.php');
        $new = include resource_path('lang/EN/new-messages.php');
        $allMessages = array_merge($translated, $new);

        $filtered = [];
        foreach ($allMessages as $key => $value) {
            $filtered[preg_replace('/[^A-Za-z0-9_]/', '_', $key)] = $value;
        }

        $string = "<?php return " . var_export($filtered, true) . ";";
        file_put_contents("{$langPath}/new-messages.php", $string);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle():string
    {
        return 'Language successfully created.';
    }
}
