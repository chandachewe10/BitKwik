<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\ZescoBillUnitsResource\Pages;
use App\Filament\Customer\Resources\ZescoBillUnitsResource\RelationManagers;
use App\Models\ZescoBills;
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

class ZescoBillUnitsResource extends Resource
{
    protected static ?string $model = ZescoBills::class;

    protected static ?string $navigationGroup = 'Bill Payments';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Pay Zesco Bills';
    protected static ?string $title = 'Pay Zesco Bills';
    protected static ?string $modelLabel = 'Pay Zesco Bills';
    protected static ?string $navigationIcon = 'heroicon-o-bolt';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Order details
                    Wizard\Step::make('Meter Details')
                        ->schema([


                            TextInput::make('meter_number')
                                ->label('Meter Number')
                                ->numeric()
                                ->required()
                                ->minLength(6)
                                ->maxLength(20),

                            TextInput::make('amount_kwacha')
                                ->label('Amount (ZMW)')
                                ->helperText('Click outside to see the update BTC & Sats')
                                ->numeric()
                                ->live(onBlur: true)
                                ->suffix('ZMW')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $amount_kwacha = floatval($state ?? 0);
                                    $amount_sats = $amount_kwacha / 0.027;
                                    $amount_btc = $amount_sats / 100000000;

                                    $set('amount_sats', round($amount_sats, 0));
                                    $set('amount_btc', round($amount_btc, 8));
                                    $set('conversion_fee', round($amount_sats * 0.08, 0));
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
                                    $set('conversion_fee', round($amount_sats * 0.08, 0));
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
                            TextInput::make('phone')
                                ->label('Phone Number for SMS token')
                                ->regex('/^(09|07)[5|6|7][0-9]{7}$/')
                                ->tel()
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
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('meter_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_kwacha')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_sats')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount_btc')
                    ->badge(),
                Tables\Columns\TextColumn::make('qr_code_path')
                    ->label('Check Invoice')
                    ->formatStateUsing(fn() => 'Check Invoice')
                    ->badge()
                    ->url(fn($record) => '/images/qrcodes/' . $record->qr_code_path),
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
            'index' => Pages\ListZescoBillUnits::route('/'),
            'create' => Pages\CreateZescoBillUnits::route('/create'),
            'view' => Pages\ViewZescoBillUnits::route('/{record}'),
            'edit' => Pages\EditZescoBillUnits::route('/{record}/edit'),
        ];
    }
}
