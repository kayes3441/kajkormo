<?php

namespace App\Filament\Resources\Admin\UserResource;

use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static? string $navigationGroup = 'User Management';
    public static function getSlug(): string
    {
        return 'user';
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->circular()->label('Photo'),
                TextColumn::make('first_name')->label('First Name')->searchable(),
                TextColumn::make('last_name')->label('Last Name')->searchable(),
                TextColumn::make('phone')->label('Phone')->searchable(),
                TextColumn::make('email')->label('Email')->sortable()->toggleable(),
                TextColumn::make('status')

                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        2 => 'danger',
                        default => 'gray',
                    })->badge()->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Verified',
                        2 => 'Suspend',
                        default => 'Pending',
                    }),
                TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
