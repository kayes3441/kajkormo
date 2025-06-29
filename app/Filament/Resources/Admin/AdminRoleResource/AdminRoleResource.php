<?php

namespace App\Filament\Resources\Admin\AdminRoleResource;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AdminRoleResource extends Resource
{
    protected static ?string $model = AdminRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static? string $navigationGroup = 'User Management';

    protected static ?string $label = 'Employee Role';

    public static function getSlug(): string
    {
        return 'employee-role';
    }
    public static function form(Form $form): Form
    {
        // Fetch modules and their actions once
        $modules = AdminPermission::all()->pluck('actions', 'module')->toArray();

        return $form->schema([
            Section::make('Role Information')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Toggle::make('status')
                        ->label('Active')
                        ->default(true)
                        ->inline(false)
                        ->onColor('success'),
                ]),
            ]),

            Section::make('Access Permission')
                ->icon('heroicon-o-lock-closed')
                ->columns([
                    'sm' => 3,
                    'md' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                ->schema(
                    collect($modules)->map(function ($actions, $module) {
                        $actions = $actions ?? [];
                        $fieldPrefix = "permissions.$module";

                        return Section::make(ucfirst($module))
                            ->schema([
                                Checkbox::make("$fieldPrefix._module")
                                    ->label('Select All')
                                    ->reactive()
                                    ->dehydrated(false) // don't save directly
                                    ->afterStateUpdated(function ($state, callable $set) use ($actions, $fieldPrefix) {
                                        foreach ($actions as $action) {
                                            $set("$fieldPrefix.$action", $state);
                                        }
                                    }),

                                Grid::make(1)->schema(
                                    collect($actions)->map(function ($action) use ($fieldPrefix, $actions) {
                                        return Checkbox::make("$fieldPrefix.$action")
                                            ->label(ucfirst(str_replace('_', ' ', $action)))
                                            ->reactive()
                                            ->dehydrated(false) // don't save directly
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) use ($fieldPrefix, $actions) {
                                                $allSelected = collect($actions)->every(function ($action) use ($get, $fieldPrefix) {
                                                    return $get("$fieldPrefix.$action") === true;
                                                });

                                                $set("$fieldPrefix._module", $allSelected);
                                            });
                                    })->toArray()
                                ),
                            ])
                            ->collapsed()
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                                'xl' => 2,
                                '2xl' => 2,
                            ]);
                    })->toArray()
                ),

            Hidden::make('access_permissions')
                ->dehydrated()
                ->dehydrateStateUsing(function (callable $get) use ($modules) {
                    $permissions = [];

                    foreach ($modules as $module => $actions) {
                        $selected = [];

                        foreach ($actions as $action) {
                            if ($get("permissions.$module.$action")) {
                                $selected[] = $action;
                            }
                        }

                        if (!empty($selected)) {
                            $permissions[$module] = $selected;
                        }
                    }
                    return $permissions;
                })
                ->afterStateHydrated(function (callable $set, $state) use ($modules) {
                    $state = json_decode($state);
                    foreach ($state ?? [] as $module => $actions) {
                        $allActions = $modules[$module] ?? [];

                        foreach ($actions as $action) {
                            $set("permissions.$module.$action", true);
                        }

                        $allSelected = collect($allActions)->every(function ($action) use ($actions) {
                            return in_array($action, $actions);
                        });

                        $set("permissions.$module._module", $allSelected);
                    }
                }),

        ]);
    }



    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->hidden(),
            TextColumn::make('name')->label('Name'),
            TextColumn::make('access_permissions')
                ->label('Access Permissions')
                ->formatStateUsing(function ($state) {
                    if (is_string($state)) {
                        $state = json_decode($state, true) ?? [];
                    }

                    return new HtmlString(
                        collect($state)
                            ->map(function ($actions, $module) {
                                $formattedModule = ucfirst(str_replace('_', ' ', $module));
                                $formattedActions = collect($actions)
                                    ->map(fn ($action) => ucfirst(str_replace('_', ' ', $action)))
                                    ->implode(', ');

                                return "<strong>{$formattedModule}</strong>: {$formattedActions}";
                            })
                            ->implode('<br>')
                    );
                })
                ->html()
                ->wrap(),
            ToggleColumn::make('status')
                ->label('Status')
                ->afterStateUpdated(function ( ) {
                    Notification::make()
                        ->title('Status Updated Successfully')
                        ->success()
                        ->send();
                }),
        ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()]);
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
            'index' => Pages\ListAdminRoles::route('/'),
            'create' => Pages\CreateAdminRole::route('/create'),
            'edit' => Pages\EditAdminRole::route('/{record}/edit'),
        ];
    }
}
