<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\SendToBankResource\Pages;
use App\Filament\Customer\Resources\SendToBankResource\RelationManagers;
use App\Models\BitCoinToBankAccount as SendToBank;
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

class SendToBankResource extends Resource
{
    protected static ?string $navigationGroup = 'BitCoin to Cash';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'BitCoin to Bank';
    protected static ?string $title = 'BitCoin to Bank';
    protected static ?string $modelLabel = 'BitCoin to Bank';
    protected static ?string $model = SendToBank::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
                                    $set('conversion_fee', round($amount_sats * 0.08, 8));
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
                                    $set('amount_sats', round($amount_sats, 8));
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('conversion_fee', round($amount_sats * 0.08, 8));
                                    $set('total_sats', round($total_sats, 8));
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

                            TextInput::make('account_number')
                                ->label('Account Number')
                                ->helperText('Enter the account number which should receive the payment')
                                ->numeric()
                                ->required()
                                ->minLength(6)
                                ->maxLength(20),
                            Select::make('bank_name')
                                ->label('Bank Name')
                                ->options([
                                    '002' => 'Absa Bank',
                                    '003' => 'Access Bank',
                                    '005' => 'Access Bank (formerly ATMA)',
                                    '006' => 'Bank of China',
                                    '007' => 'Citibank',
                                    '008' => 'Ecobank',
                                    '010' => 'Indo Zambia Bank',
                                    '011' => 'Investrust Bank',
                                    '012' => 'First Alliance Bank',
                                    '013' => 'First Capital',
                                    '014' => 'FNB',
                                    '016' => 'Stanbic Bank',
                                    '017' => 'Standard Chartered Bank',
                                    '022' => 'United Bank for Africa',
                                    '023' => 'Zanaco',
                                    '025' => 'ZICB',
                                    '028' => 'AB Bank',
                                    '032' => 'Natsave',
                                    '033' => 'Access Bank',
                                    '036' => 'Bayport',
                                    '037' => 'Zambia National Building Society',
                                ])
                                ->searchable()
                                ->required(),
                            TextInput::make('bank_branch')
                                ->label('Bank Branch')
                                ->required(),
                            TextInput::make('bank_sort_code')
                                ->label('Bank Sort Code')
                                ->required(),
                            TextInput::make('bank_account_type')
                                ->label('Bank Account Type')
                                ->required(),

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
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_kwacha')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_sats')
                    ->badge(),
                Tables\Columns\TextColumn::make('convenience_fee')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_btc')
                    ->badge(),
                Tables\Columns\TextColumn::make('network_fee')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_sats')
                    ->badge(),
                 ImageColumn::make('qr_code_path')
                 ->label('QR Code')
                 ->getStateUsing(fn ($record) => asset('images/qrcodes/' . $record->qr_code_path))
                 ->height(150),
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
            'index' => Pages\ListSendToBanks::route('/'),
            'create' => Pages\CreateSendToBank::route('/create'),
            'view' => Pages\ViewSendToBank::route('/{record}'),
            'edit' => Pages\EditSendToBank::route('/{record}/edit'),
        ];
    }
}
