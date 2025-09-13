<?php

namespace Bishopm\Hgrh\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class FileBrowser extends Component
{
    public string $currentDir = '';
    public array $files = [];
    public array $breadcrumbs = [];
    public string $error = '';
    public string $sortBy = 'name'; // name, size, date
    public string $sortDirection = 'asc';
    public string $filter = '';
    public bool $showHidden = false;

    protected string $baseDir = '';
    protected array $allowedExtensions = [
        'pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'xlsx', 'xls', 'csv', 'ppt', 'pptx', 'zip', 'rar', '7z', 'mp4', 'mp3', 'wav'
    ];

    protected array $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];

    public function mount(string $initialPath = ''): void
    {
        $this->baseDir = public_path('storage');
        $this->ensureBaseDirExists();
        $this->currentDir = $this->sanitizePath($initialPath);
        $this->loadFiles();
    }

    /**
     * Ensure the base directory exists
     */
    protected function ensureBaseDirExists(): void
    {
        if (!File::exists($this->baseDir)) {
            File::makeDirectory($this->baseDir, 0755, true);
        }
    }

    /**
     * Sanitize and validate a path - improved security
     */
    protected function sanitizePath(string $path): string
    {
        // Normalize path separators
        $path = str_replace('\\', '/', $path);
        
        // Remove dangerous characters but keep safe ones for filenames
        $path = preg_replace('/[^\w\-_\/.\s]/', '', $path);
        $path = trim($path, '/');
        
        // Remove any path traversal attempts more thoroughly
        $path = preg_replace('/\.\.+/', '', $path);
        $path = preg_replace('/\/+/', '/', $path);
        
        // Remove leading dots to prevent hidden directory access
        $path = ltrim($path, '.');
        
        return $path;
    }

    /**
     * Get the full absolute path with enhanced security validation
     */
    protected function getSecurePath(string $relativePath = ''): string
    {
        $relativePath = $this->sanitizePath($relativePath);
        $fullPath = $this->baseDir . ($relativePath ? '/' . $relativePath : '');
        
        // Normalize path separators
        $fullPath = str_replace('\\', '/', $fullPath);
        
        // Get canonical paths for comparison
        $realPath = realpath($fullPath);
        $realBaseDir = realpath($this->baseDir);
        
        // If realpath fails, the path doesn't exist - that's okay for some operations
        if ($realPath === false) {
            $realPath = $fullPath;
        }
        
        // Security: Ensure we can't escape the base directory
        if (!str_starts_with($realPath, $realBaseDir . '/') && $realPath !== $realBaseDir) {
            throw new \InvalidArgumentException('Path traversal attempt detected');
        }

        return $fullPath;
    }

    /**
     * Check if a file extension is allowed
     */
    protected function isAllowedFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions);
    }

    /**
     * Check if file should be shown based on hidden file settings
     */
    protected function shouldShowFile(string $filename): bool
    {
        if (!$this->showHidden && str_starts_with($filename, '.')) {
            return false;
        }
        return true;
    }

    /**
     * Format file size for display
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 1) . ' ' . $units[$pow];
    }

    /**
     * Get file icon based on extension - using Bootstrap icons
     */
    protected function getFileIcon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'bi-file-earmark-pdf',
            'doc', 'docx' => 'bi-file-earmark-word',
            'txt' => 'bi-file-earmark-text',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' => 'bi-file-earmark-image',
            'xlsx', 'xls', 'csv' => 'bi-file-earmark-excel',
            'ppt', 'pptx' => 'bi-file-earmark-ppt',
            'zip', 'rar', '7z' => 'bi-file-earmark-zip',
            'mp4' => 'bi-file-earmark-play',
            'mp3', 'wav' => 'bi-file-earmark-music',
            default => 'bi-file-earmark'
        };
    }

    /**
     * Load and organize files for the current directory
     */
    public function loadFiles(): void
    {
        $this->error = '';
        
        try {
            $currentPath = $this->getSecurePath($this->currentDir);
            
            if (!File::exists($currentPath)) {
                $this->currentDir = '';
                $currentPath = $this->baseDir;
                $this->error = 'Directory not found, returning to root.';
            }

            if (!File::isDirectory($currentPath)) {
                $this->currentDir = '';
                $currentPath = $this->baseDir;
                $this->error = 'Invalid directory, returning to root.';
            }

            $items = $this->scanDirectory($currentPath);
            $this->files = $this->sortAndFilterItems($items);
            $this->updateBreadcrumbs();
            
        } catch (\Exception $e) {
            logger()->error('FileBrowser error: ' . $e->getMessage(), [
                'currentDir' => $this->currentDir,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error = 'Error loading directory. Please try again.';
            $this->files = [];
            $this->breadcrumbs = [];
            $this->currentDir = '';
        }
    }

    /**
     * Scan directory and return organized file/folder data
     */
    protected function scanDirectory(string $path): Collection
    {
        if (!is_readable($path)) {
            throw new \Exception('Directory is not readable');
        }

        // Get directories
        $folders = collect(File::directories($path))->filter(function ($folder) {
            $folderName = basename($folder);
            return $this->shouldShowFile($folderName);
        })->map(function ($folder) {
            $folderPath = str_replace('\\', '/', $folder);
            $relativePath = $this->getRelativePath($folderPath);
            
            return [
                'name' => basename($folder),
                'path' => $relativePath,
                'isDir' => true,
                'size' => 0,
                'modified' => File::lastModified($folder),
                'icon' => 'bi-folder-fill',
                'formatted_size' => '-',
                'formatted_date' => date('M j, Y H:i', File::lastModified($folder))
            ];
        });

        // Get files
        $files = collect(File::files($path))
            ->filter(function ($file) {
                $filename = $file->getFilename();
                return $this->isAllowedFile($filename) && $this->shouldShowFile($filename);
            })
            ->map(function ($file) {
                $filePath = str_replace('\\', '/', $file->getPathname());
                $relativePath = $this->getRelativePath($filePath);
                
                return [
                    'name' => $file->getFilename(),
                    'path' => $relativePath,
                    'isDir' => false,
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'icon' => $this->getFileIcon($file->getFilename()),
                    'formatted_size' => $this->formatFileSize($file->getSize()),
                    'formatted_date' => date('M j, Y H:i', $file->getMTime()),
                    'extension' => strtolower($file->getExtension())
                ];
            });

        return $folders->merge($files);
    }

    /**
     * Get relative path from absolute path
     */
    protected function getRelativePath(string $absolutePath): string
    {
        $relativePath = str_replace($this->baseDir . '/', '', $absolutePath);
        return trim($relativePath, '/');
    }

    /**
     * Sort and filter items based on current settings
     */
    protected function sortAndFilterItems(Collection $items): array
    {
        // Apply filter
        if ($this->filter) {
            $items = $items->filter(function ($item) {
                return str_contains(strtolower($item['name']), strtolower($this->filter));
            });
        }

        // Sort items - directories first, then by selected column
        $items = $items->sort(function ($a, $b) {
            // Always put directories first
            if ($a['isDir'] && !$b['isDir']) return -1;
            if (!$a['isDir'] && $b['isDir']) return 1;
            
            // Then sort by the selected column
            $aValue = $a[$this->sortBy];
            $bValue = $b[$this->sortBy];
            
            if ($this->sortBy === 'name') {
                $result = strcasecmp($aValue, $bValue);
            } else {
                $result = $aValue <=> $bValue;
            }
            
            return $this->sortDirection === 'desc' ? -$result : $result;
        });

        return $items->values()->toArray();
    }

    /**
     * Update breadcrumbs based on current directory
     */
    protected function updateBreadcrumbs(): void
    {
        $this->breadcrumbs = $this->currentDir 
            ? array_filter(explode('/', $this->currentDir))
            : [];
    }

    /**
     * Navigate to a specific directory
     */
    public function navigateTo(string $path): void
    {
        try {
            $sanitizedPath = $this->sanitizePath($path);
            $fullPath = $this->getSecurePath($sanitizedPath);
            
            if (!File::exists($fullPath) || !File::isDirectory($fullPath)) {
                $this->error = 'Directory does not exist or is not accessible.';
                return;
            }

            $this->currentDir = $sanitizedPath;
            $this->loadFiles();
        } catch (\Exception $e) {
            $this->error = 'Navigation error: Unable to access directory.';
            logger()->error('FileBrowser navigation error: ' . $e->getMessage());
        }
    }

    /**
     * Navigate to parent directory
     */
    public function goUp(): void
    {
        if (!$this->currentDir) return;

        $parts = explode('/', $this->currentDir);
        array_pop($parts);
        $this->currentDir = implode('/', $parts);
        $this->loadFiles();
    }

    /**
     * Navigate using breadcrumb
     */
    public function navigateToBreadcrumb(int $index): void
    {
        if ($index < 0) {
            $this->currentDir = '';
        } else {
            $parts = explode('/', $this->currentDir);
            $newParts = array_slice($parts, 0, $index + 1);
            $this->currentDir = implode('/', $newParts);
        }
        $this->loadFiles();
    }

    /**
     * Sort files by specified column
     */
    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->loadFiles();
    }

    /**
     * Toggle hidden files visibility
     */
    public function toggleHidden(): void
    {
        $this->showHidden = !$this->showHidden;
        $this->loadFiles();
    }

    /**
     * Clear any error messages
     */
    public function clearError(): void
    {
        $this->error = '';
    }

    /**
     * Reset filter
     */
    public function clearFilter(): void
    {
        $this->filter = '';
        $this->loadFiles();
    }

    /**
     * Apply filter when updated
     */
    public function updatedFilter(): void
    {
        $this->loadFiles();
    }

    /**
     * Get the public URL for a file with proper mime type detection
     */
    public function getFileUrl(string $filePath): string
    {
        return asset('storage/' . $filePath);
    }

    /**
     * Download a file
     */
    public function downloadFile(string $filePath): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $fullPath = $this->getSecurePath($filePath);
            
            if (!File::exists($fullPath) || File::isDirectory($fullPath)) {
                abort(404, 'File not found');
            }

            if (!$this->isAllowedFile(basename($fullPath))) {
                abort(403, 'File type not allowed');
            }

            return response()->download($fullPath);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }
    }

    public function render()
    {
        return view('hgrh::livewire.file-browser');
    }
}