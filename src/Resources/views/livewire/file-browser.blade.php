<div class="file-browser">
    {{-- Custom Styles --}}
    <style>
    .file-browser .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .file-browser .modal.show {
        display: block !important;
    }

    .file-browser .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
    }

    .file-browser iframe {
        width: 100%;
        height: 100%;
    }
    </style>

    {{-- Error Messages --}}
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PDF Viewer Modal --}}
    @if($selectedFile)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                            {{ $selectedFile['name'] }}
                            @if($selectedFile['name'] !== $selectedFile['filename'])
                                <small class="text-muted">({{ $selectedFile['filename'] }})</small>
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeViewer" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        @if($selectedFile['description'])
                            <div class="bg-info bg-opacity-10 px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Description:</small>
                                <div class="text-dark">{{ $selectedFile['description'] }}</div>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center bg-light px-3 py-2 border-bottom">
                            <div class="text-muted small">
                                Size: {{ $selectedFile['size'] }} | Modified: {{ $selectedFile['modified'] }}
                            </div>
                            <div>
                                <a href="{{ $selectedFile['url'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    Open in New Tab
                                </a>
                                <a href="{{ $selectedFile['url'] }}" download class="btn btn-outline-secondary btn-sm ms-2">
                                    <i class="bi bi-download me-1"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                        <iframe src="{{ $selectedFile['url'] }}" 
                                width="100%" 
                                height="600" 
                                style="border: none; min-height: 70vh;"
                                title="{{ $selectedFile['name'] }}">
                            <p>Your browser does not support iframes. 
                               <a href="{{ $selectedFile['url'] }}" target="_blank">Click here to view the PDF</a>
                            </p>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- File Browser Interface --}}
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-folder2-open me-2"></i>
                    Document Browser
                </h5>
            </div>
        </div>

        <div class="card-body">
            {{-- Breadcrumb Navigation --}}
            @if(!empty($breadcrumbs) || !empty($currentPath))
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item {{ empty($currentPath) ? 'active' : '' }}">
                            @if(empty($currentPath))
                                <i class="bi bi-house-fill me-1"></i>
                                Home
                            @else
                                <a href="#" wire:click.prevent="navigateToFolder('')" class="text-decoration-none">
                                    <i class="bi bi-house-fill me-1"></i>
                                    Home
                                </a>
                            @endif
                        </li>
                        @foreach($breadcrumbs as $crumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $crumb['name'] }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="#" wire:click.prevent="navigateToFolder('{{ $crumb['path'] }}')" class="text-decoration-none">
                                        {{ $crumb['name'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif

            {{-- Current Path Display --}}
            <div class="row mb-3">
                <div class="col">
                    <div class="bg-light p-2 rounded border">
                        <small class="text-muted">Current Location:</small>
                        <strong>{{ $currentPath ?: '/' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Directory and File Listing --}}
            <div class="row">
                <div class="col">
                    @if(empty($directories) && empty($files))
                        <div class="text-center py-5">
                            <i class="bi bi-folder-x display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No items found</h5>
                            <p class="text-muted">This directory is empty.</p>
                        </div>
                    @else
                        <div class="list-group">
                            {{-- Parent Directory Link --}}
                            @if($hasParentDirectory)
                                <button type="button" 
                                        class="list-group-item list-group-item-action d-flex align-items-center py-3"
                                        wire:click="navigateToFolder('..')">
                                    <i class="bi bi-arrow-up-circle text-secondary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">.. (Parent Directory)</h6>
                                        <small class="text-muted">Go up one level</small>
                                    </div>
                                </button>
                            @endif

                            {{-- Directories --}}
                            @foreach($directories as $directory)
                                <button type="button" 
                                        class="list-group-item list-group-item-action d-flex align-items-center py-3"
                                        wire:click="navigateToFolder('{{ $directory['name'] }}')">
                                    <i class="bi bi-folder-fill text-warning me-3" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $directory['name'] }}</h6>
                                        <small class="text-muted">Folder</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </button>
                            @endforeach

                            {{-- PDF Files --}}
                            @foreach($files as $file)
                                <button type="button" 
                                        class="list-group-item list-group-item-action d-flex align-items-center py-3"
                                        wire:click="selectFile('{{ $file['filename'] }}')">
                                    <i class="bi bi-file-earmark-pdf text-danger me-3" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">
                                            {{ $file['name'] }}
                                            @if(!$file['has_document_record'])
                                                <small class="text-warning">
                                                    <i class="bi bi-exclamation-triangle-fill" title="No database record found"></i>
                                                </small>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            {{ $file['size'] }} • Modified: {{ $file['modified'] }}
                                            @if($file['name'] !== $file['filename'])
                                                <br>
                                                <span class="text-muted fst-italic">File: {{ $file['filename'] }}</span>
                                            @endif
                                            @if($file['description'])
                                                <br>
                                                <span class="text-muted">{{ Str::limit($file['description'], 100) }}</span>
                                            @endif
                                        </small>
                                    </div>
                                    <i class="bi bi-eye text-muted"></i>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

