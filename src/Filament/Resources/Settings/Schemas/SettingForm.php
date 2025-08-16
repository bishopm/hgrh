<?php

namespace Bishopm\Hgrh\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('setting')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('value')
                    ->required(),
            ]);
    }
}
