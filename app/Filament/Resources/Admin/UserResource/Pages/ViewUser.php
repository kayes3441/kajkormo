<?php

namespace App\Filament\Resources\Admin\UserResource\Pages;

use App\Filament\Resources\Admin\UserResource\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
class ViewUser extends ViewRecord
{
    protected static string $resource =  UserResource::class;

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Group::make([
                            ImageEntry::make('image')
                                ->label('Profile Photo')
                                ->circular()
                                ->height(120)
                                ->width(120)
                                ->alignCenter(),
                            TextEntry::make('full_name')
                                ->label('')
                                ->size('xl')
                                ->weight('bold')
                                ->formatStateUsing(fn ($record) => trim($record->first_name . ' ' . $record->last_name))
                                ->alignCenter(),
                            TextEntry::make('phone')
                                ->icon('heroicon-o-phone')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('email')
                                ->icon('heroicon-o-envelope')
                                ->label('')
                                ->alignCenter(),
                        ])->columnSpanFull(),
                    ]),

                // 🪪 Identification Details
                Section::make('Identification Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('gender')
                                ->badge()
                                ->label('Gender')
                                ->color(fn ($state) => match ($state) {
                                    'male' => 'info',
                                    'female' => 'pink',
                                    'other' => 'gray',
                                    default => 'gray',
                                }),
                            TextEntry::make('date_of_birth')
                                ->label('Date of Birth')
                                ->date(),
                            TextEntry::make('phone_verified_at')
                                ->label('Phone Verified')
                                ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn ($state) => $state ? 'success' : 'danger'),
                            TextEntry::make('email_verified_at')
                                ->label('Email Verified')
                                ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn ($state) => $state ? 'success' : 'danger'),
                        ]),
                    ]),

                Section::make('Address')
                    ->schema([
                        TextEntry::make('address')
                            ->label('')
                            ->placeholder('No address provided'),
                    ]),

                Section::make('Account Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('app_language')
                                ->label('App Language'),

                            TextEntry::make('status')
                                ->label('Account Status')
                                ->badge()
                                ->color(fn ($state) => match ((int) $state) {
                                    1 => 'success',
                                    2 => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn ($state) => match ((int) $state) {
                                    1 => 'Verified',
                                    2 => 'Suspend',
                                    default => 'Pending',
                                }),
                            TextEntry::make('created_at')
                                ->label('Joined At')
                                ->dateTime('M d, Y'),
                            TextEntry::make('login_attempts')
                                ->label('Login Attempts'),
                        ]),
                    ]),
            ]);
    }
    public function updateStatus($userId, $status):void
    {
        $user = User::find($userId);
        $user->update(['status' => $status]);
        Notification::make()
            ->title('User status updated!')
            ->success()
            ->send();
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Change Status')
                ->action(function ($record, array $data) {
                    $record->status = $data['status'];
                    $record->save();
//                    $this->notify('success', 'Status updated!');
                })
                ->form([
                    Select::make('status')
                        ->options([
                            0 => 'Pending',
                            1 => 'Verified',
                            2 => 'Suspend',
                        ])
                        ->default(fn ($record) => $record->status),
                ])
        ];
    }
}
