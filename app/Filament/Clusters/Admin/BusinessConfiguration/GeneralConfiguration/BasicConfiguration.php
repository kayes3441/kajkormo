<?php

namespace App\Filament\Clusters\Admin\BusinessConfiguration\GeneralConfiguration;

use Filament\Clusters\Cluster;

class  BasicConfiguration extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static? string $navigationGroup = 'Business Configuration';
}
