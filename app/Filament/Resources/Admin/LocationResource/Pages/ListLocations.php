<?php

namespace App\Filament\Resources\Admin\LocationResource\Pages;

use App\Filament\Resources\Admin\LocationResource\LocationResource;
use App\Models\Location;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->icon('heroicon-o-bars-3')
                ->badge(Location::count()),

            'Division' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'division'))
                ->badge(Location::where('level', 'division')->count()),

            'Metropolitan' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'metropolitan'))
                ->badge(Location::where('level', 'metropolitan')->count()),

            'District' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'district'))
                ->badge(Location::where('level', 'district')->count()),

            'Sub‑district' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'subdistrict'))
                ->badge(Location::where('level', 'subdistrict')->count()),
        ];
    }
}
