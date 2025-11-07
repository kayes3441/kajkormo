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
                    RepeatableEntry::make('images_url')
                        ->label('Images')
                        ->schema([
                            ImageEntry::make('')
                                ->label('')
                                ->height(150)
                                ->width(150)
                                ->defaultImageUrl(url('/post/placeholder.png')),
                        ])
                        ->columns(5),
                ]),
            Section::make('User Identity')
                ->schema([
                    Grid::make(2)
                    ->schema([
                        TextEntry::make('user.userIdentity.identity_type')
                            ->label('Identity Type')
                            ->formatStateUsing(fn ($record) => strtoupper($record->user?->userIdentity?->identity_type ?? '—'))->inlineLabel(),

                        TextEntry::make('user.userIdentity.status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'pending' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state ?? 'Unknown'))->inlineLabel(),

                        TextEntry::make('user.userIdentity.identity_number')
                            ->label('Identity Number')
                            ->formatStateUsing(fn ($record) => $record->user?->userIdentity?->nid_number ?? '—'),
                        ImageEntry::make('user.userIdentity.front_image_url')
                            ->label('')
                            ->height(150)
                            ->width(150)
                            ->defaultImageUrl(url('/post/placeholder.png'))->columnSpanFull()->extraAttributes(['class' => 'mx-auto flex-col']),
                        ImageEntry::make('user.userIdentity.back_image_url')
                            ->label('')
                            ->height(150)
                            ->width(150)
                            ->defaultImageUrl(url('/post/placeholder.png'))->columnSpanFull()->extraAttributes(['class' => 'mx-auto flex-col']),
                    ]),
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
