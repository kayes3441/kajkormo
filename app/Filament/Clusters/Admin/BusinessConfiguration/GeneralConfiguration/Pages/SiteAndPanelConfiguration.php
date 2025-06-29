<?php

namespace App\Filament\Clusters\Admin\BusinessConfiguration\GeneralConfiguration\Pages;

use App\Filament\Clusters\Admin\BusinessConfiguration\GeneralConfiguration\BasicConfiguration;
use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed $form
 */
class SiteAndPanelConfiguration extends Page
{
    protected static string $view = 'filament.clusters.admin.business-configuration.basic-configuration.pages.site-and-panel-configuration';

    protected static ?string $cluster = BasicConfiguration::class;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'web_header_logo' => Setting::get('web_header_logo'),
            'web_footer_logo' => Setting::get('web_footer_logo'),
            'web_fav_icon' => Setting::get('web_fav_icon'),
            'web_loading_gif' => Setting::get('web_loading_gif'),
            'app_header_logo' => Setting::get('app_header_logo'),
            'panel_primary_color' => Setting::get('panel_primary_color'),
        ]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Branding & Loading')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 8,
                    ])
                    ->description('This section covers the brand logo and loading animations used to create a strong visual identity and smooth user experience during app or site startup.')
                    ->schema([
                        Section::make([
                            FileUpload::make('web_header_logo')
                                ->label('Upload Website Header Logo')
                                ->image()
                                ->imageEditor()
                                ->directory('business_configuration')
                                ->imagePreviewHeight('150')
                                ->maxSize(2048)
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),

                        Section::make([
                            FileUpload::make('app_header_logo')
                                ->label('Upload Website Header Logo App')
                                ->image()
                                ->imageEditor()
                                ->directory('business_configuration')
                                ->imagePreviewHeight('150')
                                ->maxSize(2048)
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),
                        Section::make([
                            FileUpload::make('web_footer_logo')
                                ->label('Upload Website Footer Logo')
                                ->image()
                                ->imageEditor()
                                ->directory('business_configuration')
                                ->imagePreviewHeight('150')
                                ->maxSize(2048)
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),
                        Section::make([
                            FileUpload::make('web_fav_icon')
                                ->label('Upload Website Fav Icon')
                                ->image()
                                ->imageEditor()
                                ->directory('business_configuration')
                                ->imagePreviewHeight('150')
                                ->maxSize(2048)
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),
                        Section::make([
                            FileUpload::make('web_loading_gif')
                                ->label('Upload Loading Gif')
                                ->image()
                                ->imageEditor()
                                ->acceptedFileTypes(['image/gif'])
                                ->directory('business_configuration')
                                ->imagePreviewHeight('150')
                                ->maxSize(2048)
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ]),
                    ])
                    ->persistCollapsed()
                    ->collapsible(),

                Section::make('Color Picker')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 8,
                    ])
                    ->description('Select a color using the picker to customize the website’s color and personalize the look and feel to match your brand or preference')
                    ->schema([
                        Section::make([
                            ColorPicker::make('panel_primary_color')
                                ->label('Select Panel Primary Color')
                                ->rgb()
                                ->regex('/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/')
                        ])->columnSpan([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),

                    ])->persistCollapsed()
                    ->collapsible(),
            ])->statePath( 'data');
    }

    public function update(): void
    {
        $data = $this->form->getState();
        $uploadKeys = [
            'web_header_logo',
            'web_footer_logo',
            'web_fav_icon',
            'web_loading_gif',
            'app_header_logo',
        ];

        foreach ($uploadKeys as $key) {
            $newPath = $data[$key] ?? null;
            $oldPath = Setting::get($key);

            if (!empty($newPath) && $oldPath !== $newPath) {
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                Setting::set($key, $newPath);
            }
        }

        Setting::set('panel_primary_color', $data['panel_primary_color'] ?? null);

        Notification::make()
            ->title('Settings updated successfully!')
            ->success()
            ->send();
    }

}
