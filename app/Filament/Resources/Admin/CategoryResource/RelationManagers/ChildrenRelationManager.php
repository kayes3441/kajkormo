<?php

namespace App\Filament\Resources\Admin\CategoryResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';
    protected static ?string $title = 'Sub Locations';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('level')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
            ])
            ->defaultSort('level');
    }
}
