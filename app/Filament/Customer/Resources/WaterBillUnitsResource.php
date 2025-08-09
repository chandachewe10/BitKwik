<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\WaterBillUnitsResource\Pages;
use App\Filament\Customer\Resources\WaterBillUnitsResource\RelationManagers;
use App\Models\WaterBillUnits;
use App\Models\ZescoBills;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WaterBillUnitsResource extends Resource
{
    protected static ?string $model = ZescoBills::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Pay Lusaka Bills';
    protected static ?string $title = 'Pay Lusaka Bills';
    protected static ?string $modelLabel = 'Pay Lusaka Bills';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListWaterBillUnits::route('/'),
            'create' => Pages\CreateWaterBillUnits::route('/create'),
            'view' => Pages\ViewWaterBillUnits::route('/{record}'),
            'edit' => Pages\EditWaterBillUnits::route('/{record}/edit'),
        ];
    }
}
