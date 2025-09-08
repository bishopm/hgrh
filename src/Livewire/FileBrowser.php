<?php

namespace Bishopm\Hgrh\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class FileBrowser extends Component
{
    public $currentDir = ''; // relative to public
    public $files = [];
    public $breadcrumbs = [];

    protected string $baseDir='';

    public function mount()
    {
        $this->baseDir = public_path('storage'); // must exist
        $this->currentDir = ''; // start at root
        $this->loadFiles();
    }

    protected function fullPath(?string $relativePath): string
    {
        $base = rtrim(str_replace('\\','/',$this->baseDir), '/');

        $relativePath = trim((string)$relativePath, '/');

        $dir = $relativePath ? $base . '/' . $relativePath : $base;

        // Normalize slashes and remove trailing slash
        $dir = rtrim(str_replace('\\','/',$dir), '/');

        // Security: prevent escaping baseDir
        if (!str_starts_with($dir, $base)) {
            abort(403, 'Access denied');
        }

        return $dir;
    }

    public function loadFiles()
    {
        $dir = $this->fullPath($this->currentDir);

        // Double-check existence
        if (!File::exists($dir)) {
            // fallback to baseDir if folder missing
            $dir = $this->baseDir;
        }

        $folders = collect([]);
        $files = collect([]);

        if (File::exists($dir)) {
            $folders = collect(File::directories($dir))
                ->map(fn($folder) => [
                    'name' => basename($folder),
                    'path' => ltrim(str_replace($this->baseDir, '', $folder), '/'),
                    'isDir' => true,
                ]);

            $files = collect(File::files($dir))
                ->map(fn($file) => [
                    'name' => $file->getFilename(),
                    'path' => ltrim(str_replace($this->baseDir, '', $file->getPathname()), '/'),
                    'isDir' => false,
                ]);
        }

        $this->files = $folders->merge($files)->values()->toArray();

        // Breadcrumbs
        $this->breadcrumbs = collect(explode('/', $this->currentDir))
            ->filter()
            ->values()
            ->all();
    }


    public function navigateTo($path)
    {
        $this->currentDir = ltrim($path, '/');
        $this->loadFiles();
    }

    public function goUp()
    {
        if (!$this->currentDir) return;

        $parts = explode('/', $this->currentDir);
        array_pop($parts);
        $this->currentDir = implode('/', $parts);
        $this->loadFiles();
    }

    public function navigateBreadcrumb($index)
    {
        $parts = explode('/', $this->currentDir);
        $newParts = array_slice($parts, 0, $index + 1);
        $this->currentDir = implode('/', $newParts);
        $this->loadFiles();
    }

    public function render()
    {
        return view('hgrh::livewire.file-browser');
    }

}
