<?php

namespace Bishopm\Hgrh\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Bishopm\Hgrh\Models\Document;

class FileBrowser extends Component
{
    public $currentPath = '';
    public $selectedFile = null;
    public $breadcrumbs = [];
    
    public function mount($path = '')
    {
        $this->currentPath = $path;
        $this->updateBreadcrumbs();
    }

    
    public function navigateToFolder($folderName)
    {
        if ($folderName === '..') {
            // Navigate up one level
            $pathParts = explode('/', $this->currentPath);
            array_pop($pathParts);
            $this->currentPath = implode('/', array_filter($pathParts));
        } elseif ($folderName === '') {
            // Navigate to root
            $this->currentPath = '';
        } else {
            // Navigate into folder
            $this->currentPath = $this->currentPath ? $this->currentPath . '/' . $folderName : $folderName;
        }
        
        $this->selectedFile = null;
        $this->updateBreadcrumbs();
    }
    
    public function selectFile($fileName)
    {
        $filePath = $this->currentPath ? $this->currentPath . '/' . $fileName : $fileName;
        
        // Check if file exists and is a PDF
        if (Storage::disk('public')->exists($filePath)) {
            // Generate the proper URL for the file
            $fileUrl = Storage::disk('public')->url($filePath);
            
            // Find the document record
            $document = Document::where('file', $fileName)->first();
            
            $this->selectedFile = [
                'name' => $document ? $document->document : $fileName,
                'filename' => $fileName,
                'path' => $filePath,
                'url' => $fileUrl,
                'description' => $document ? $document->description : null,
                'size' => $this->formatFileSize(Storage::disk('public')->size($filePath)),
                'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($filePath))
            ];
        }
    }
    
    public function closeViewer()
    {
        $this->selectedFile = null;
    }
    
    private function updateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        
        if (empty($this->currentPath)) {
            return;
        }
        
        $pathParts = explode('/', $this->currentPath);
        $buildPath = '';
        
        foreach ($pathParts as $part) {
            $buildPath = $buildPath ? $buildPath . '/' . $part : $part;
            $this->breadcrumbs[] = [
                'name' => $part,
                'path' => $buildPath
            ];
        }
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
    
    public function getDirectoriesAndFiles()
    {
        $directories = [];
        $files = [];
        
        try {
            // Get directories
            $dirs = Storage::disk('public')->directories($this->currentPath);
            foreach ($dirs as $dir) {
                $directories[] = [
                    'name' => basename($dir),
                    'type' => 'directory'
                ];
            }
            
            // Get files
            $allFiles = Storage::disk('public')->files($this->currentPath);
            
            // Get all document records for files in this directory for efficiency
            $fileNames = array_map('basename', $allFiles);
            $documents = Document::whereIn('file', $fileNames)->get()->keyBy('file');

            foreach ($allFiles as $file) {
                $filename = basename($file);

                $document = $documents->get($filename);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                $files[] = [
                    'filename' => $filename,
                    'name' => $document ? $document->document : $filename,
                    'description' => $document ? $document->description : null,
                    'type' => $extension,
                    'size' => $this->formatFileSize(Storage::disk('public')->size($file)),
                    'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)),
                    'has_document_record' => $document !== null,
                ];
            }

        } catch (\Exception $e) {
            // Handle any storage errors gracefully
            session()->flash('error', 'Unable to access the requested directory: ' . $e->getMessage());
        }
        
        // Sort directories and files alphabetically by display name
        usort($directories, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        usort($files, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        
        return compact('directories', 'files');
    }
    
    public function render()
    {
        $content = $this->getDirectoriesAndFiles();
        
        return view('hgrh::livewire.file-browser', [
            'directories' => $content['directories'],
            'files' => $content['files'],
            'hasParentDirectory' => !empty($this->currentPath)
        ]);
    }
}