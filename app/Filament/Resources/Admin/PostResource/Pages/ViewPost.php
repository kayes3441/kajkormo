<?php

namespace App\Filament\Resources\Admin\PostResource\Pages;
use App\Filament\Resources\Admin\PostResource\PostResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord; 
use Filament\Infolists;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\Action;
use Filament\Forms;
class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->schema([
            Section::make('Post Overview')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('title')
                                ->label('Title')
                                ->weight('bold')
                                ->size('xl'),
                            TextEntry::make('user.first_name')
                                ->label('Author')
                                ->icon('heroicon-o-user'),
                            TextEntry::make('price')
                                ->label('Price')
                                ->money('USD'),
                            TextEntry::make('work_type')->label('Work Type'),
                            TextEntry::make('payment_type')->label('Payment Type'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => match ((int) $state) {
                                    1 => 'success',
                                    2 => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn ($state) => match ((int) $state) {
                                    1 => 'Published',
                                    2 => 'Rejected',
                                    default => 'Pending',
                                }),
                        ]),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->markdown(),
                ]),

            Section::make('Images')
                ->schema([
                    RepeatableEntry::make('images')
                        ->schema([
                            ImageEntry::make('')
                                ->label('')
                                ->height(150)
                                ->width(150) ,
                        ])
                        ->columns(5),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Update Status')
                ->icon('heroicon-o-adjustments-horizontal')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Select Status')
                        ->options([
                            0 => 'Pending',
                            1 => 'Published',
                            2 => 'Rejected',
                        ])
                        ->default(fn ($record) => $record->status)
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'status' => $data['status'],
                        'published_at' => $data['status'] == 1 ? now() : null,
                    ]);
                    Notification::make()
                        ->title('Post status updated successfully!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
