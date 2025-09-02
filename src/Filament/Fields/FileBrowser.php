<?php

namespace Bishopm\Hgrh\Filament\Fields;
use Closure;
use Filament\Forms\Components\Field;

class FileBrowser extends Field
{
    protected string $view = 'hgrh::forms.components.file-browser';

    public array $files = [
        'one',
        'two',
    ];

    public function files(Clos $files): static {
        $this->files = $files;

        return $this;
    }
}
