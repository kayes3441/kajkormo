<?php

namespace App\Filament\Resources\Admin\PostResource;

use App\Filament\Resources\Admin\PostResource\Pages\ListPosts;
use App\Filament\Resources\Admin\PostResource\Pages\ViewPost;
use App\Models\Post;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static? string $navigationGroup = 'Post Management';

    protected static ?string $label = 'Post List';

    public static function getSlug(): string
    {
        return 'post';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('user.first_name')->label('Author')->sortable(),

                TextColumn::make('status')

                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        2 => 'danger',
                        default => 'gray',
                    })->badge()->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Published',
                        2 => 'Rejected',
                        default => 'Pending',
                    }),
                TextColumn::make('price')->money('BDT')->sortable(),
//                TextColumn::make('published_at')->dateTime('M d, Y')->label('Published'),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => ListPosts::route('/'),
            'view' =>  ViewPost::route('/{record}'),

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
