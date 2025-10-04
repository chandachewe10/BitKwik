<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\SendToMobileResource\Pages;
use App\Filament\Customer\Resources\SendToMobileResource\RelationManagers;
use App\Models\BitCoinToMobileMoney as SendToMobile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Tables\Columns\ImageColumn;

class SendToMobileResource extends Resource
{
    protected static ?string $navigationGroup = 'BitCoin to Cash';
    protected static ?int $navigationSort = 1;
    protected static ?string $model = SendToMobile::class;
    protected static ?string $navigationLabel = 'BitCoin to Mobile';
    protected static ?string $pluralModelLabel = 'BitCoin to Mobile';
    protected static ?string $title = 'BitCoin to Mobile';
    protected static ?string $modelLabel = 'BitCoin to Mobile';
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Order details
                    Wizard\Step::make('Account Details')
                        ->schema([


                            TextInput::make('amount_sats')
                                ->helperText('Click outside to see the update ZMW & BTC')
                                ->label('Amount in Sats')
                                ->suffix('SATS')
                                ->numeric()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_sats = floatval($state ?? 0);
                                    $amount_btc = $amount_sats / 100000000;
                                    $amount_kwacha = $amount_sats * 0.026;
                                    $total_sats = $amount_sats + ($amount_sats * 0.08) + 100;
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('amount_kwacha', round($amount_kwacha, 2));
                                    $set('conversion_fee', round($amount_sats * 0.08, 0));
                                    $set('total_sats', round($total_sats, 8));
                                })
                                ->required()
                                ->disabled(false)
                                ->maxValue(5000)
                                ->minValue(200),

                            TextInput::make('amount_kwacha')
                                ->helperText('Click outside to see the update BTC & Sats')
                                ->label('Amount (ZMW)')
                                ->numeric()
                                ->live(onBlur: true)
                                ->required()
                                ->suffix('ZMW')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_kwacha = floatval($state ?? 0);
                                    $amount_sats = $amount_kwacha / 0.026;
                                    $amount_btc = $amount_sats / 100000000;
                                    $total_sats = $amount_sats + ($amount_sats * 0.08) + 100;
                                    $set('total_sats', round($total_sats, 8));
                                    $set('amount_sats', round($amount_sats, 8));
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('conversion_fee', round($amount_sats * 0.08, 8));
                                })
                                ->minValue(1),


                            TextInput::make('amount_btc')
                                ->helperText('Amount in Bitcoin')
                                ->label('BTC')
                                ->suffix('BTC')
                                ->required()
                                ->readOnly()
                                ->minValue(0.00000001),
                            TextInput::make('conversion_fee')
                                ->helperText('BTC Convenience Rate @8%')
                                ->label('Bitcoin to Cash Service Fee')
                                ->suffix('SATS')
                                ->required()
                                ->readOnly()
                                ->minValue(0.00000001),
                            TextInput::make('network_fee')
                                ->helperText('Bitcoin/Lightning network fee')
                                ->label('Network Fee')
                                ->suffix('SATS')
                                ->required()
                                ->readOnly()
                                ->default(100),


                            TextInput::make('total_sats')
                                ->helperText('Total sats required (Amount + Service Fee + Network Fee)')
                                ->label('Total Invoice (SATS)')
                                ->suffix('SATS')
                                ->readOnly()
                                ->required()
                                ->afterStateHydrated(function (Get $get, Set $set) {
                                    $amount_sats = floatval($get('amount_sats') ?? 0);
                                    $conversion_fee = floatval($get('conversion_fee') ?? 0);
                                    $network_fee = floatval($get('network_fee') ?? 0);
                                    $set('total_sats', $amount_sats + $conversion_fee + $network_fee);
                                })


                        ]),

                    // Step 2: Delivery information
                    Wizard\Step::make('Delivery Information')
                        ->schema([

                            TextInput::make('mobile_number')
                                ->label('Mobile Number')
                                ->tel()
                                ->helperText('Enter the mobile number which should receive the payment')
                                ->numeric()
                                ->regex('/^(09|07)[5|6|7][0-9]{7}$/')
                                ->required()
                                ->minLength(6)
                                ->maxLength(20),
                            TextInput::make('email')
                                ->label('Email for payment confirmation')
                                ->email()
                                ->nullable(),
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
                Tables\Columns\TextColumn::make('mobile_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_kwacha')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_sats')
                    ->badge(),
                Tables\Columns\TextColumn::make('convenience_fee')
                    ->badge(),
                Tables\Columns\TextColumn::make('network_fee')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_sats')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_btc')
                    ->badge(),

                Tables\Columns\TextColumn::make('checkout_url')

                    ->label('QR Code')
                    ->formatStateUsing(function ($state) {
                        return "<a href='{$state}' target='_blank' class='text-primary underline'>Check Qrcode</a>";
                    })
                    ->badge()
                    ->html()
                    ->searchable(),


                Tables\Columns\TextColumn::make('lightning_invoice_address')
                    ->label('Lightning Invoice')
                    ->formatStateUsing(fn($state) => $state ? 'Copy Invoice' : 'No Invoice')
                    ->copyable()
                    ->copyMessage('Invoice copied to clipboard!')
                    ->copyMessageDuration(1500)
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
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
            'index' => Pages\ListSendToMobiles::route('/'),
            'create' => Pages\CreateSendToMobile::route('/create'),
            'view' => Pages\ViewSendToMobile::route('/{record}'),
            'edit' => Pages\EditSendToMobile::route('/{record}/edit'),
        ];
    }
}
