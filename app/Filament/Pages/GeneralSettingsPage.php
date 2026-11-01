<?php

namespace App\Filament\Pages;

use App\Models\GeneralSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GeneralSettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'General Settings';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.general-settings';

    public ?array $data = [];

    protected array $keys = [
        'delivery_returns' => '',
        'paystack_enabled' => true,
        'whatsapp_enabled' => false,
        'account_name' => '',
        'account_number' => '',
        'bank_name' => '',
    ];

    public function mount(): void
    {
        $state = [];

        foreach ($this->keys as $key => $default) {
            $row = GeneralSetting::where('key', $key)->first();
            $state[$key] = $row ? $row->value : $default;
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Store Information')->schema([
                    Textarea::make('delivery_returns')
                        ->label('Delivery & Returns Text')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

                Section::make('Payment Methods')->schema([
                    Toggle::make('paystack_enabled')->label('Enable Paystack'),
                    Toggle::make('whatsapp_enabled')->label('Enable WhatsApp Payments'),
                ])->columns(2),

                Section::make('Bank Account Details')->schema([
                    TextInput::make('account_name')->required()->maxLength(255),
                    TextInput::make('account_number')->required()->maxLength(50),
                    TextInput::make('bank_name')->required()->maxLength(255),
                ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach (array_keys($this->keys) as $key) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $state[$key] ?? null],
            );
        }

        Notification::make()->title('Settings saved')->success()->send();
    }
}