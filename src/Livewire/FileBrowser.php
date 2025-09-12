<?php

namespace Bishopm\Hgrh\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class FileBrowser extends Component
{
    public $currentDir = ''; // relative to baseDir
    public $files = [];
    public $breadcrumbs = [];

    protected string $baseDir = '';

    public function mount()
    {
        $this->baseDir = public_path('storage');

        // Ensure baseDir exists
        if (!File::exists($this->baseDir)) {
            File::makeDirectory($this->baseDir, 0755, true);
        }

        $this->currentDir = ''; // root
        $this->loadFiles();
    }

    /**
     * Compute full absolute path from a relative path
     */
    protected function fullPath(?string $relativePath): string
    {
        $relativePath = trim((string) $relativePath, '/');
        $dir = $this->baseDir . ($relativePath ? '/' . $relativePath : '');
        $dir = str_replace('\\','/', $dir);

        // Security: cannot escape baseDir
        if (!str_starts_with($dir, $this->baseDir)) {
            abort(403, 'Access denied');
        }

        return $dir;
    }

    /**
     * Load folders and files for the current directory
     */
    public function loadFiles()
    {
        $currentDir = trim((string) $this->currentDir, '/');
        $dir = $this->fullPath($currentDir);

        // fallback to baseDir if dir missing
        if (!File::exists($dir)) {
            $dir = $this->baseDir;
            $currentDir = '';
            $this->currentDir = '';
        }

        $folders = collect([]);
        $files = collect([]);

        if (File::exists($dir)) {
            // Folders
            $folders = collect(File::directories($dir))
                ->map(function ($folder) {
                    $folder = str_replace('\\','/',$folder);
                    $relative = trim(str_replace($this->baseDir . '/', '', rtrim($folder,'/')), '/');
                    return [
                        'name' => basename($folder),
                        'path' => $relative,
                        'isDir' => true,
                    ];
                });

            // Files
            $files = collect(File::files($dir))
                ->map(function ($file) {
                    $filePath = str_replace('\\','/',$file->getPathname());
                    $relative = trim(str_replace($this->baseDir . '/', '', $filePath), '/');
                    return [
                        'name' => $file->getFilename(),
                        'path' => $relative ?: $file->getFilename(),
                        'isDir' => false,
                    ];
                });
        }

        $this->files = $folders->merge($files)->values()->toArray();

        // Breadcrumbs
        $this->breadcrumbs = collect(explode('/', $currentDir))
            ->filter()
            ->values()
            ->all();
    }

    public function navigateTo($path)
    {
        $path = trim((string) $path, '/');

        if ($this->currentDir) {
            // Append folder to current path
            $this->currentDir = $this->currentDir . '/' . $path;
        } else {
            $this->currentDir = $path;
        }

        $this->currentDir = trim($this->currentDir, '/');
        $this->loadFiles();
    }


    /**
     * Go up one folder
     */
    public function goUp()
    {
        if (!$this->currentDir) return;

        $parts = explode('/', $this->currentDir);
        array_pop($parts);
        $this->currentDir = implode('/', $parts);
        $this->loadFiles();
    }

    /**
     * Navigate via breadcrumb
     */
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
