<?php

namespace App\Filament\Resources\Admin\LocationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(100),

            Select::make('level')
                ->options([
                    'metropolitan' => 'Metropolitan',
                    'district'     => 'District',
                    'sub_district'  => 'Sub‑district',
                ])
                ->live()
                ->required(),
        ]);

    }

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
