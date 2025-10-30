<?php

namespace App\Filament\Clusters\Admin\BusinessConfiguration\BasicConfiguration\Pages;

use App\Filament\Clusters\Admin\BusinessConfiguration\BasicConfiguration\BasicConfiguration;
use App\Models\Setting;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Actions\Action as FormAction;

/**
 * @property mixed $form
 */
class SMSConfiguration extends Page
{
    protected static string $view = 'filament.clusters.admin.business-configuration.basic-configuration.pages.sms-configuration';

    protected static ?string $cluster = BasicConfiguration::class;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'sms_config_status' => Setting::get('sms_config_status'),
            'sms_api_key' => Setting::get('sms_api_key'),
            'sms_senderid' => Setting::get('sms_senderid'),
            'sms_template' => Setting::get('sms_template'),
            'sms_url' => Setting::get('sms_url'),
        ]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('SMS Configuration')
                    ->headerActions($this->getSMSStatusAction())
                    ->description('Configure your SMS API credentials and sender details.')                    ->schema([
                        Grid::make(columns: 2)->schema(
                            [
                                TextInput::make('sms_api_key')
                                    ->label('API Key')
                                    ->default(Setting::get('sms_api_key')),
                                TextInput::make('sms_senderid')
                                    ->label('Sender ID')
                                    ->default(Setting::get('sms_senderid')),
                                TextInput::make('sms_url')
                                    ->label('Sender ID')
                                    ->default(Setting::get('sms_senderid')),
                                Textarea::make('sms_template')
                                    ->label('SMS Template')
                                    ->default(Setting::get('sms_template'))
                                    ->helperText('Use {otp} to insert the OTP code into the message.'),
                            ]
                        )
                    ])->persistCollapsed()
                    ->collapsible(),
            ])->statePath( 'data');
    }
    protected function getSMSStatusAction(): array
    {
        $status = Setting::get('sms_config_status', false);

        return [
            FormAction::make('toggle_sms_status')
                ->label($status ? 'Disable SMS' : 'Enable SMS')
                ->color($status ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading('Confirm SMS Status Change')
                ->modalDescription(
                    $status
                        ? 'Are you sure you want to disable SMS notifications?'
                        : 'Are you sure you want to enable SMS notifications?'
                )
                ->action(function () use ($status) {
                    Setting::set('sms_config_status', ! $status);
                    Notification::make()
                        ->title($status ? 'SMS Disabled' : 'SMS Enabled')
                        ->success()
                        ->send();

                    $this->form->fill([
                        'sms_config_status' => ! $status,
                    ]);
                }),
        ];
    }
    public function update(): void
    {
        $data = $this->form->getState();
        $keys = [
            'sms_api_key',
            'sms_senderid',
            'sms_template',
            'sms_url',
        ];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                Setting::set($key, $data[$key]);
            }
        }

        Notification::make()
            ->title('Settings updated successfully!')
            ->success()
            ->send();
    }

}
