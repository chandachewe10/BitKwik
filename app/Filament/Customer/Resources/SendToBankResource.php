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
                            TextInput::make('amount_kwacha')
                                ->helperText('Click outside to see the update BTC & Sats')
                                ->label('Amount (ZMW)')
                                ->numeric()
                                ->live(onBlur: true)
                                ->required()
                                ->suffix('ZMW')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_kwacha = floatval($state ?? 0);
                                    $amount_sats = $amount_kwacha / 0.027;
                                    $amount_btc = $amount_sats / 100000000;

                                    $set('amount_sats', round($amount_sats, 0));
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('conversion_fee', round($amount_btc * 0.08, 8));
                                })
                                ->minValue(1),

                            TextInput::make('amount_sats')
                                ->helperText('Amount in Satoshis')
                                ->label('Sats')
                                ->suffix('SAT')
                                ->numeric()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_sats = floatval($state ?? 0);
                                    $amount_btc = $amount_sats / 100000000;
                                    $amount_kwacha = $amount_sats * 0.027;

                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('amount_kwacha', round($amount_kwacha, 2));
                                    $set('conversion_fee', round($amount_btc * 0.08, 8));
                                })
                                ->required()
                                ->disabled(false)
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
                                ->suffix('SAT')
                                ->required()
                                ->readOnly()
                                ->minValue(0.00000001),

                        ]),

                    // Step 2: Delivery information
                    Wizard\Step::make('Delivery Information')
                        ->schema([

                            TextInput::make('account_number')
                                ->label('Account Number')
                                ->helperText('Enter the account number to receive the payment')
                                ->numeric()
                                ->required()
                                ->minLength(6)
                                ->maxLength(20),
                            TextInput::make('bank_name')
                                ->label('Bank Name')
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
                                ->label('Email for receipt')
                                ->email()
                                ->nullable(),
                        ]),


                ])->columnSpan('full')
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
            'index' => Pages\ListSendToBanks::route('/'),
            'create' => Pages\CreateSendToBank::route('/create'),
            'view' => Pages\ViewSendToBank::route('/{record}'),
            'edit' => Pages\EditSendToBank::route('/{record}/edit'),
        ];
    }
}
