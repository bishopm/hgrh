<div class="card shadow-sm">
    <div class="card-body">
        <!-- Header with title and controls -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h5 class="card-title mb-0">
                <i class="bi bi-folder2-open me-2"></i>File Browser
            </h5>
            
            <!-- Filter and controls -->
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <input 
                        wire:model.live.debounce.300ms="filter"
                        type="text" 
                        placeholder="Filter files..."
                        class="form-control form-control-sm ps-5"
                        style="width: 200px;"
                    >
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    @if($filter)
                        <button 
                            wire:click="clearFilter"
                            class="btn btn-sm btn-link position-absolute top-50 end-0 translate-middle-y me-1 p-1"
                            type="button"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    @endif
                </div>
                
                <button 
                    wire:click="toggleHidden"
                    class="btn btn-sm btn-outline-secondary"
                    title="Toggle hidden files"
                >
                    <i class="bi bi-{{ $showHidden ? 'eye-slash' : 'eye' }}"></i>
                </button>
            </div>
        </div>

        <!-- Error Display -->
        @if($error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ $error }}
                <button 
                    type="button" 
                    class="btn-close" 
                    wire:click="clearError"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <button 
                        wire:click="navigateToBreadcrumb(-1)" 
                        class="btn btn-link p-0 text-decoration-none {{ $currentDir === '' ? 'fw-bold text-dark' : '' }}"
                    >
                        <i class="bi bi-house-door me-1"></i>Home
                    </button>
                </li>
                
                @foreach($breadcrumbs as $index => $crumb)
                    <li class="breadcrumb-item {{ $index === count($breadcrumbs) - 1 ? 'active fw-bold' : '' }}">
                        @if($index === count($breadcrumbs) - 1)
                            {{ $crumb }}
                        @else
                            <button 
                                wire:click="navigateToBreadcrumb({{ $index }})"
                                class="btn btn-link p-0 text-decoration-none"
                            >
                                {{ $crumb }}
                            </button>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- Navigation Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <small class="text-muted">
                {{ count($files) }} {{ count($files) === 1 ? 'item' : 'items' }}
                @if($filter)
                    <span class="badge bg-info ms-1">filtered</span>
                @endif
                @if($showHidden)
                    <span class="badge bg-secondary ms-1">showing hidden</span>
                @endif
            </small>
            
            @if($currentDir)
                <button 
                    wire:click="goUp" 
                    class="btn btn-sm btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>Up
                </button>
            @endif
        </div>

        <!-- File List -->
        @if(count($files) > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 50%;">
                                <button 
                                    wire:click="sortBy('name')" 
                                    class="btn btn-sm btn-link p-0 text-decoration-none text-dark d-flex align-items-center"
                                >
                                    Name
                                    @if($sortBy === 'name')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="text-center" style="width: 15%;">
                                <button 
                                    wire:click="sortBy('size')" 
                                    class="btn btn-sm btn-link p-0 text-decoration-none text-dark d-flex align-items-center justify-content-center w-100"
                                >
                                    Size
                                    @if($sortBy === 'size')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" style="width: 25%;">
                                <button 
                                    wire:click="sortBy('modified')" 
                                    class="btn btn-sm btn-link p-0 text-decoration-none text-dark d-flex align-items-center"
                                >
                                    Modified
                                    @if($sortBy === 'modified')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $file['isDir'] ? 'bi-folder-fill text-warning' : $file['icon'] . ' text-primary' }} me-2"></i>
                                        @if($file['isDir'])
                                            <button 
                                                wire:click="navigateTo('{{ $file['path'] }}')" 
                                                class="btn btn-link p-0 text-decoration-none text-start text-truncate"
                                                style="max-width: 300px;"
                                            >
                                                {{ $file['name'] }}
                                            </button>
                                        @else
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                target="_blank" 
                                                class="text-decoration-none text-dark text-truncate"
                                                title="Open {{ $file['name'] }}"
                                                style="max-width: 300px;"
                                            >
                                                {{ $file['name'] }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle text-muted small">
                                    {{ $file['formatted_size'] }}
                                </td>
                                <td class="align-middle text-muted small">
                                    {{ $file['formatted_date'] }}
                                </td>
                                <td class="align-middle">
                                    @if(!$file['isDir'])
                                        <div class="btn-group" role="group">
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                                title="View file"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                download
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Download file"
                                            >
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach($files as $file)
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1 min-w-0">
                                    <i class="bi {{ $file['isDir'] ? 'bi-folder-fill text-warning' : $file['icon'] . ' text-primary' }} me-2"></i>
                                    @if($file['isDir'])
                                        <button 
                                            wire:click="navigateTo('{{ $file['path'] }}')" 
                                            class="btn btn-link p-0 text-decoration-none text-start text-truncate"
                                        >
                                            <div>
                                                <div class="fw-medium">{{ $file['name'] }}</div>
                                                <small class="text-muted">{{ $file['formatted_date'] }}</small>
                                            </div>
                                        </button>
                                    @else
                                        <div class="flex-grow-1 min-w-0 me-2">
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                target="_blank" 
                                                class="text-decoration-none text-dark"
                                            >
                                                <div class="fw-medium text-truncate">{{ $file['name'] }}</div>
                                                <small class="text-muted">{{ $file['formatted_size'] }} • {{ $file['formatted_date'] }}</small>
                                            </a>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a 
                                                href="{{ asset('storage/' . $file['path']) }}" 
                                                download
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-folder2-open display-1 text-muted mb-3"></i>
                <h6 class="text-muted">No files found</h6>
                <p class="text-muted small">
                    @if($filter)
                        No files match your search criteria.
                        <button wire:click="clearFilter" class="btn btn-link btn-sm p-0">Clear filter</button>
                    @else
                        This directory is empty.
                    @endif
                </p>
            </div>
        @endif

        <!-- Loading indicator -->
        <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="bg-white p-4 rounded shadow d-flex align-items-center">
                <div class="spinner-border text-primary me-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span>Loading...</span>
            </div>
        </div>
    </div>
</div>