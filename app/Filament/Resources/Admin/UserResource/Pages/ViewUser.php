<?php

namespace App\Filament\Resources\Admin\UserResource\Pages;

use App\Filament\Resources\Admin\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource\UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('User Status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active',
                        2 => 'Banned',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update(['status' => $state]);
                        Notification::make()
                            ->title('Status updated successfully!')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
