<?php

namespace Bishopm\Hgrh\Filament\Forms;

use Filament\Forms\Components\Field;

class FilePicker extends Field
{
    protected string $view = 'hgrh::forms.components.file-picker';

    public function directory(string $path): static
    {
        $this->extraAttributes(['data-directory' => $path]);

        return $this;
    }
}
