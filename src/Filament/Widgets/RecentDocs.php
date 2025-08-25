<?php

namespace Bishopm\Hgrh\Filament\Widgets;

use Bishopm\Hgrh\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentDocs extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Document::query()->limit(10))
            ->columns([
                TextColumn::make('document')
                    ->searchable(),
                IconColumn::make('publish')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->recordUrl(fn ($record) => 
                route('filament.admin.resources.documents.edit', [
                    'record' => $record->id,
                ])
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
