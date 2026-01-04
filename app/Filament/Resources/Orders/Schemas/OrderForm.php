<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\City;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('phone')->required(),
                    ]),

                Section::make('Address')
                    ->columns(2)
                    ->schema([
                        Select::make('state_id')
                            ->label('State')
                            ->options(State::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Select::make('city_id')
                            ->label('City')
                            ->options(function ($get) {
                                $stateId = $get('state_id');
                                return $stateId ? City::where('state_id', $stateId)->pluck('name', 'id') : [];
                            })
                            ->required()
                            ->searchable(),

                        TextInput::make('address')->columnSpanFull(),
                        TextInput::make('postal_code'),
                    ]),

                Section::make('Order Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),

                        Select::make('payment_method')
                            ->options([
                                'paystack' => 'Paystack',
                                'whatsapp' => 'Whatsapp',
                            ]),

                        Select::make('delivery_method')
                            ->options([
                                'pickup' => 'Pickup',
                                'delivery' => 'Delivery',
                            ]),

                        TextInput::make('subtotal')->numeric()->disabled(),
                        TextInput::make('total')->numeric()->disabled(),

                        Textarea::make('order_note')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
