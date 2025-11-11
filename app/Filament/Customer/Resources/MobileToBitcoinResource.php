<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\MobileToBitcoinResource\Pages;
use App\Filament\Customer\Resources\MobileToBitcoinResource\RelationManagers;
use App\Models\MobileToBitcoin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Tables\Columns\ImageColumn;

class MobileToBitcoinResource extends Resource
{
    protected static ?string $model = MobileToBitcoin::class;

    protected static ?string $navigationGroup = 'Cash to BitCoin';
    protected static ?string $navigationLabel = 'Buy Bitcoin';
    protected static ?string $pluralModelLabel = 'Buy Bitcoins';
    protected static ?string $title = 'Cash to BitCoin';
    protected static ?string $modelLabel = 'Cash to BitCoin';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
return $form
            ->schema([
                Wizard::make([
                    // Step 1: Order details
                    Wizard\Step::make('Account Details')
                        ->schema([
                               TextInput::make('amount_kwacha')
                                ->helperText('Click outside to see the update BTC & Sats')
                                ->label('Amount (ZMW)')
                                ->numeric()
                                ->live(onBlur: true)
                                ->required()
                                ->suffix('ZMW')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_kwacha = floatval($state ?? 0);
                                    $amount_sats = $amount_kwacha / 0.023;
                                    $amount_btc = $amount_sats / 100000000;
                                    $total_amount = $amount_kwacha + ($amount_kwacha * 0.08) + 5;
                                    $set('total_amount', round($total_amount, 2));
                                    $set('amount_sats', round($amount_sats, 8));
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('conversion_fee', round($amount_kwacha * 0.08, 2));
                                })
                                ->minValue(50),



                            TextInput::make('amount_sats')
                               
                                ->label('Amount in Sats')
                                ->suffix('SATS')
                                ->numeric()
                                 ->required()
                                 ->readOnly()
                                ->maxValue(100000)
                                ->minValue(2000),

                            
                            TextInput::make('amount_btc')
                                ->helperText('Amount in Bitcoin')
                                ->label('BTC')
                                ->suffix('BTC')
                                ->required()
                                ->readOnly()
                                ->minValue(0.00000001),
                            TextInput::make('conversion_fee')
                                ->helperText('BTC Convenience Rate @8%')
                                ->label('Cash to Bitcoin Service Fee')
                                ->suffix('SATS')
                                ->required()
                                ->readOnly()
                                ,
                            TextInput::make('network_fee')
                                ->helperText('Bitcoin/Lightning network fee')
                                ->label('Network Fee')
                                ->suffix('ZMW')
                                ->required()
                                ->readOnly()
                                ->default(5),


                            TextInput::make('total_amount')
                                ->helperText('Total amount required (Amount + Service Fee + Network Fee)')
                                ->label('Total Invoice (ZMW)')
                                ->suffix('ZMW')
                                ->readOnly()
                                ->required()
                               


                        ]),

                    


                ])->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                
                Tables\Columns\TextColumn::make('amount_kwacha')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_sats')
                    ->badge(),
                Tables\Columns\TextColumn::make('convenience_fee')
                    ->badge(),
                Tables\Columns\TextColumn::make('network_fee')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('amount_btc')
                    ->badge(),


Tables\Columns\TextColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->formatStateUsing(fn($state) => $state ? 'View QR Code' : 'No QR Code')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->action(
                        Tables\Actions\Action::make('viewQrCode')
                            ->label('View QR Code')
                            ->modalHeading('QR Code')
                            ->modalContent(fn($record) => $record->qr_code_path 
                                ? new \Illuminate\Support\HtmlString('<div style="text-align: center;"><img src="' . asset('images/qrcodes/' . $record->qr_code_path) . '" alt="QR Code" style="max-width: 100%; height: auto;" /></div>')
                                : new \Illuminate\Support\HtmlString('<p>No QR code available</p>'))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),



                Tables\Columns\TextColumn::make('lnurl')
                    ->label('Lightning Invoice')
                    ->formatStateUsing(fn($state) => $state ? 'Copy Invoice' : 'No Invoice')
                    ->copyable()
                    ->copyMessage('Invoice copied to clipboard!')
                    ->copyMessageDuration(1500)
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge(),
                Tables\Columns\TextColumn::make('is_used')
                    ->formatStateUsing(fn($state) => $state ? 'true' : 'false')
                    ->badge(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMobileToBitcoins::route('/'),
            'create' => Pages\CreateMobileToBitcoin::route('/create'),
            'view' => Pages\ViewMobileToBitcoin::route('/{record}'),
            'edit' => Pages\EditMobileToBitcoin::route('/{record}/edit'),
        ];
    }
}
