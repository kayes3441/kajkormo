<?php

namespace App\Filament\Resources\Admin\BannerResource;

use App\Models\Banner;
use App\Trait\FileManagerTrait;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    use FileManagerTrait;
    protected static ?string $model = Banner::class;
    protected static? string $navigationGroup = 'Promotion Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Banner Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Banner Title')
                                ->required()
                                ->maxLength(100),

                            Select::make('type')
                                ->label('Banner Type')
                                ->required()
                                ->options([
                                    'homepage' => 'Homepage',
                                    'category' => 'Category',
                                    'offer'    => 'Offer',
                                    'custom'   => 'Custom',
                                ])
                                ->searchable(),

                            TextInput::make('url')
                                ->label('Redirect URL')
                                ->url()
                                ->maxLength(255),

                            Toggle::make('status')
                                ->label('Active')
                                ->default(false)
                                ->inline(false),

                            FileUpload::make('image')
                                ->label('Banner Image')
                                ->image()
                                ->directory('banners')
                                ->imageEditor()
                                ->panelLayout('center')
                                ->maxSize(2048)
                                ->required()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->columnSpanFull(),


                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
