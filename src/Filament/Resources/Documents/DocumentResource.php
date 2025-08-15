<?php

namespace Bishopm\Hgrh\Filament\Resources\Documents;

use Bishopm\Hgrh\Filament\Resources\Documents\Pages\CreateDocument;
use Bishopm\Hgrh\Filament\Resources\Documents\Pages\EditDocument;
use Bishopm\Hgrh\Filament\Resources\Documents\Pages\ListDocuments;
use Bishopm\Hgrh\Filament\Resources\Documents\Schemas\DocumentForm;
use Bishopm\Hgrh\Filament\Resources\Documents\Tables\DocumentsTable;
use BackedEnum;
use Bishopm\Hgrh\Models\Document;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'document';

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
