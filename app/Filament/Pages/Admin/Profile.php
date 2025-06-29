<?php

namespace App\Filament\Pages\Admin;


use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed $form
 */
class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.admin.profile';
    protected static ?string $title = 'Profile';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public static function getAdmin(): ?object
    {
        return auth('admin')->user();
    }
    public array $data = [];

    public function mount(): void
    {

        $admin = $this->getAdmin();
        $this->form->fill([
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'image' => $admin->image,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Avatar')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Profile Image')
                                ->avatar()
                                ->imageEditor()
                                ->directory('profile')
                                ->columnSpanFull(),
                        ]),

                    Step::make('Info')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Full Name')
                                        ->maxLength(255),
                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->maxLength(255),

                                    TextInput::make('phone')
                                        ->label('Phone')
                                ]),
                        ]),

                    Step::make('Password')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('password')
                                        ->label('New Password')
                                        ->password()
                                        ->minLength(8)
                                        ->maxLength(255)
                                        ->confirmed()
                                        ->revealable()

                                        ->helperText('Minimum 8 characters'),

                                    TextInput::make('password_confirmation')
                                        ->label('Confirm Password')
                                        ->password()
                                        ->maxLength(255),
                                ]),

                        ]),
                ])->skippable()
                    ->submitAction(
                        Action::make('update')
                            ->label('Update Profile')
                            ->button()
                            ->submit('update')
                    )
            ])
            ->statePath( 'data');
    }

    public function update(): void
    {
        $admin = $this->getAdmin();
        $data = $this->form->getState();
        $admin->name = $data['name'] ?? $admin->name;
        $admin->email = $data['email'] ?? $admin->email;
        $admin->phone = $data['phone'] ?? $admin->phone;

        if (!empty($data['password'])) {
            $admin->password = bcrypt($data['password']);
        }

        if (!empty($data['image'])) {
            if (!empty($admin->image) && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }
            $admin->image = $data['image'];
        }
        $admin->save();

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }
}
