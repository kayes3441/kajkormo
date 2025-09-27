<?php
namespace App\Filament\Resources\Admin\NotificationResource\Pages;

use App\Filament\Resources\Admin\NotificationResource\NotificationResource;
use App\Models\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ManageFirebaseConfig extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = NotificationResource::class; // 👈 key difference
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.admin.notification.manage-firebase-config';

    public ?array $data = [];
    public ?string $firebase_config = null;

    public function mount(): void
    {
        $this->firebase_config = Setting::where('key', 'firebase_config')->value('value');

        $this->form->fill([
            'firebase_config' => $this->firebase_config,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Textarea::make('firebase_config')
                ->label('Firebase JSON')
                ->rows(10)
                ->required(),
        ];
    }

    public function submit(): void
    {
        Setting::updateOrCreate(
            ['key' => 'firebase_config'],
            ['value' => $this->form->getState()['firebase_config']]
        );

        Notification::make()
            ->title('Firebase configuration saved!')
            ->success()          // success style (green)
            ->send();
    }
}
