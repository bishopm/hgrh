<div class="space-y-2 p-4 border rounded shadow bg-white">

    <!-- Breadcrumb -->
    <div class="flex flex-wrap items-center gap-1 mb-2">
        <span wire:click="navigateTo('')" class="text-blue-600 cursor-pointer hover:underline">Home</span>
        @foreach($breadcrumbs as $i => $crumb)
            <span>/</span>
            <span wire:click="navigateBreadcrumb({{ $i }})" class="text-blue-600 cursor-pointer hover:underline">
                {{ $crumb }}
            </span>
        @endforeach
    </div>

    <!-- Up Button -->
    <div class="flex justify-end mb-2">
        @if($currentDir)
            <button wire:click="goUp" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                ⬆ Up
            </button>
        @endif
    </div>

    <!-- File list -->
    <ul class="divide-y border-t border-b list-unstyled">
        @foreach($files as $file)
            <li class="p-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center">
                @if($file['isDir'])
                    <span wire:click="navigateTo('{{ $file['path'] }}')" class="text-blue-600 hover:underline cursor-pointer">
                        📁 {{ $file['name'] }}
                    </span>
                @else
                    <a href="{{ asset($file['path']) }}" target="_blank" class="text-gray-800 hover:text-blue-600">
                        📄 {{ $file['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
