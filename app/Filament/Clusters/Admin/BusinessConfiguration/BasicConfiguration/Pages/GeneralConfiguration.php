<?php

namespace App\Filament\Clusters\Admin\BusinessConfiguration\BasicConfiguration\Pages;

use App\Filament\Clusters\Admin\BusinessConfiguration\BasicConfiguration\BasicConfiguration;
use App\Models\Setting;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * @property mixed $form
 */
class GeneralConfiguration extends Page
{
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.admin.business-configuration.basic-configuration.pages.general-configuration';

    protected static ?string $cluster = BasicConfiguration::class;


    public ?bool $maintenance_mode_status = null;

    public ?string $maintenance_mode_start_at = null;
    public ?string $maintenance_mode_end_at = null;
    public ?string $business_name = null;
    public ?string $business_email = null;
    public ?string $business_country = null;
    public ?string $business_phone = null;
    public ?string $timezone = null;
    public ?string $address = null;
    public ?string $copyright_text = null;
    public ?string $cookies_text = null;
    public ?string $privacy_policy = null;
    public ?string $terms_and_conditions = null;

    public function mount(): void
    {
        $this->maintenance_mode_status = Setting::get('maintenance_mode_status');
        $this->maintenance_mode_start_at = Setting::get('maintenance_mode_start_at');
        $this->maintenance_mode_end_at = Setting::get('maintenance_mode_end_at');
        $this->business_name = Setting::get('business_name');
        $this->business_email = Setting::get('business_email');
        $this->business_country = Setting::get('business_country');
        $this->business_phone = Setting::get('business_phone');
        $this->timezone = Setting::get('timezone');
        $this->address = Setting::get('address');
        $this->copyright_text = Setting::get('copyright_text');
        $this->cookies_text = Setting::get('cookies_text');
        $this->privacy_policy = Setting::get('privacy_policy');
        $this->terms_and_conditions = Setting::get('terms_and_conditions');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Maintenance Mode')
                    ->headerActions($this->getMaintenanceModeStatusAction())
                    ->description('Enabling maintenance mode will temporarily disable the selected systems starting from your specified date and time.')
                    ->schema([
                        Grid::make(columns: 2)->schema(
                            [
                                DateTimePicker::make('maintenance_mode_start_at')
                                    ->label('Start Time')
                                    ->native(false),

                                DateTimePicker::make('maintenance_mode_end_at')
                                    ->label('End Time')
                                    ->native(false),
                            ]
                        )
                    ])->persistCollapsed()
                    ->collapsible(),

                Section::make('Basic Information')
                    ->description('Setup your all basic business information.')
                    ->schema([
                        Grid::make(columns: 3)->schema(
                            [
                                TextInput::make('business_name')
                                    ->label('Business Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('business_email')
                                    ->label('Business Email')
                                    ->email()
                                    ->required(),

                                Select::make('business_country')
                                    ->label('Business Country')
                                    ->searchable()
//                                    ->options(
//                                        Country::query()->pluck('name', 'id') // requires `squire` package
//                                    )
                                    ->required(),

                                TextInput::make('business_phone')
                                    ->label('Business Phone')
                                    ->tel()
                                    ->required(),

                                Select::make('timezone')
                                    ->label('Timezone')
                                    ->searchable()
                                    ->options(
                                        collect(timezone_identifiers_list())
                                            ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                    )
                                    ->required(),

                                TextInput::make('address')
                                    ->label('Business Address')
                                    ->required()
                            ]
                        )
                    ])->persistCollapsed()
                    ->collapsible(),
                Section::make('Terms & Conditions')
                    ->description('Manage your website’s Terms & Conditions content.')
                    ->schema([
                        RichEditor::make('terms_and_conditions')
                            ->label('Terms & Conditions')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'link', 'orderedList', 'bulletList', 'blockquote'
                            ])
                            ->helperText('This will be displayed on your Terms & Conditions page.'),
                    ])
                    ->persistCollapsed()
                    ->collapsible(),

                Section::make('Privacy Policy')
                    ->description('Manage your website’s Privacy Policy content.')
                    ->schema([
                        RichEditor::make('privacy_policy')
                            ->label('Privacy Policy')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'link', 'orderedList', 'bulletList', 'blockquote'
                            ])
                            ->helperText('This will be displayed on your Privacy Policy page.'),
                    ])
                    ->persistCollapsed()
                    ->collapsible(),
                Section::make('Copyright & Cookies')
                    ->description('Configure your copyright and cookies information for the website.')
                    ->schema([
                        Textarea::make('copyright_text')
                            ->label('Copyright Text')
                            ->placeholder('Enter your copyright text here.')
                            ->rows(4)
                            ->required()
                            ->helperText('This text will appear at the footer of your site.'),

                        Textarea::make('cookies_text')
                            ->label('Cookies Policy')
                            ->placeholder('Enter your cookies policy here.')
                            ->rows(6)
                            ->required()
                            ->helperText('This text will be used for the cookies information section.'),
                    ])->persistCollapsed()
                    ->collapsible(),
            ]);
    }
    protected function getMaintenanceModeStatusAction(): array
    {
        $status = $this->maintenance_mode_status;
        return [
            FormAction::make('toggle_maintenance_mode')
                ->label($status ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode')
                ->color($status ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading('Confirm Maintenance Mode Change')
                ->modalDescription(
                    $status
                        ? 'Are you sure you want to disable maintenance mode and bring the application back online?'
                        : 'Are you sure you want to enable maintenance mode and temporarily take the application offline?'
                )
                ->action(function () use ($status) {
                    Setting::set('maintenance_mode_status', ! $status);
                    Notification::make()
                        ->title($status ? 'Maintenance Mode Disabled' : 'Maintenance Mode Enabled')
                        ->success()
                        ->send();
                    $this->form->fill([
                        'maintenance_mode_status' => ! $status,
                    ]);
                })
        ];
    }


    public function update():void
    {
        Setting::set('maintenance_mode_status', $this->maintenance_mode_status);
        Setting::set('maintenance_mode_start_at', $this->maintenance_mode_start_at);
        Setting::set('maintenance_mode_end_at', $this->maintenance_mode_end_at);
        Setting::set('business_name', $this->business_name);
        Setting::set('business_email', $this->business_email);
        Setting::set('business_country', $this->business_country);
        Setting::set('business_phone', $this->business_phone);
        Setting::set('timezone', $this->timezone);
        Setting::set('address', $this->address);
        Setting::set('copyright_text', $this->copyright_text);
        Setting::set('cookies_text', $this->cookies_text);
        Setting::set('terms_and_conditions', $this->terms_and_conditions);
        Setting::set('privacy_policy', $this->privacy_policy);

        Notification::make()
            ->title('General Configuration updated successfully!')
            ->success()
            ->send();
    }
}
