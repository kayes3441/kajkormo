<?php

namespace App\Filament\Resources\Admin\EmployeeResource;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Trait\AdminPermissionTrait;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeResource extends Resource
{
    use AdminPermissionTrait;
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $label = 'Employees';
    protected static? string $navigationGroup = 'User Management';

    public static function getSlug(): string
    {
        return 'employees';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Employee Info')
                        ->description('Employee\'s personal details')
                        ->schema([
                            Grid::make(columns: 2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Full Name')
                                        ->placeholder('Example: John Doe')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('phone')
                                        ->label('Phone')
                                        ->tel()
                                        ->placeholder('Example: 017xxxxxx')
                                        ->required()
                                        ->maxLength(20),

                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->placeholder('Example: johndoe@example.com')
                                        ->required()
                                        ->maxLength(255)
                                        ->rule(fn ($record) => Rule::unique(Admin::class, 'email')
                                            ->ignore($record?->id)),

                                    FileUpload::make('image') // renamed to avoid duplicate key
                                    ->label('Upload Profile Image')
                                        ->image()
                                        ->directory('profile')
                                        ->imageEditor()
                                        ->imagePreviewHeight('150')
                                        ->maxSize(2048)
                                        ->required(),
                                ]),
                        ]),

                    Step::make('Identification')
                        ->description('Employee`s identification details')
                        ->schema([
                            Grid::make(columns: 2)
                                ->schema([
                                    Select::make('identify_type')
                                        ->label('Identity Type')
                                        ->options([
                                            'nid' => 'National ID',
                                            'passport' => 'Passport',
                                            'driving_license' => 'Driving License',
                                        ])
                                        ->required()
                                        ->searchable()
                                        ->native(false),

                                    TextInput::make('identify_number')
                                        ->label('Identity Number')
                                        ->placeholder('Enter Identity Number')
                                        ->required()
                                        ->maxLength(100),

                                    FileUpload::make('identify_image')
                                        ->label('Upload Identity Image')
                                        ->image()
                                        ->directory('profile/identity')
                                        ->imageEditor()
                                        ->imagePreviewHeight('150')
                                        ->maxSize(2048)
                                        ->required(),
                                   Select::make('admin_role_id')
                                    ->label('Employee Role')
                                       ->searchable()
                                       ->required()
                                       ->options(AdminRole::pluck('name', 'id'))
                                ]),
                        ]),

                    Step::make('Security')
                        ->description('Set a strong password')
                        ->schema([
                            Grid::make(columns: 2)
                                ->schema([
                                    TextInput::make('password')
                                        ->label('Password')
                                        ->password()
                                        ->minLength(8)
                                        ->revealable()
                                        ->same('password_confirmation')
                                        ->autocomplete('new-password')
                                        ->placeholder('Enter a strong password')
                                        ->required(fn(Page $livewire): bool=> $livewire instanceof CreateRecord)
                                        ->dehydrateStateUsing(fn($state) =>bcrypt($state)),


                                    TextInput::make('password_confirmation')
                                        ->label('Confirm Password')
                                        ->password()
                                        ->required(fn(Page $livewire): bool=> $livewire instanceof CreateRecord)
                                        ->autocomplete('new-password')
                                        ->placeholder('Re-enter the password'),
                                ]),
                        ]),
                ])
            ])->columns(1);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Employee ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable(),

                // Admin Role Column (assuming `role` is a relation)
//                TextColumn::make('admin_role.name')
//                    ->label('Role')
//                    ->sortable()
//                    ->searchable(),

                // Image Column (Displaying employee profile image)

                // Email Column
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                // Action Column (Edit and Delete buttons)
                ToggleColumn::make('status')
                    ->label('Status')
                    ->afterStateUpdated(function ($state, $record) {
                        Notification::make()
                            ->title('Status Updated Successfully')
                            ->success()
                            ->send();
                    }),
                TextColumn::make('actions')
                    ->label('Actions')
                    ->formatStateUsing(function ($state, $record) {
                        return view('filament.pages.employee-actions', ['employee' => $record]);
                    }),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
