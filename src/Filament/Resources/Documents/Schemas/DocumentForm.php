<?php

namespace Bishopm\Hgrh\Filament\Resources\Documents\Schemas;

use Bishopm\Hgrh\Filament\Fields\FileManager;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document')
                    ->label('Document name')
                    ->required(),
                FileUpload::make('file')
                    ->required(),
                FileManager::make('filename'),
                RichEditor::make('description')
                    ->columnSpanFull(),
                Select::make('tags')->label('Subject tags')
                    ->relationship('tags','name',modifyQueryUsing: fn (Builder $query) => $query->where('type','document'))
                    ->multiple()
                    ->createOptionForm([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('type')
                                    ->default('document')
                                    ->readonly()
                                    ->required(),
                                TextInput::make('slug')
                                    ->required(),
                            ])
                    ]),
                Toggle::make('publish'),
            ]);
    }
}
