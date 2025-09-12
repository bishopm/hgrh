<div class="space-y-2 p-4 border rounded shadow bg-white">

    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-1 mb-2">
        <span 
            wire:click="navigateTo('')" 
            class="cursor-pointer hover:underline {{ $currentDir === '' ? 'font-bold text-gray-900' : 'text-blue-600' }}">
            Home
        </span>
        @foreach($breadcrumbs as $i => $crumb)
            <span>/</span>
            <span 
                wire:click="navigateBreadcrumb({{ $i }})"
                class="cursor-pointer hover:underline {{ $i === count($breadcrumbs) - 1 ? 'font-bold text-gray-900' : 'text-blue-600' }}">
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

    <!-- File and folder list -->
    <ul class="divide-y border-t border-b list-unstyled">
        @foreach($files as $file)
            <li class="p-2 hover:bg-gray-100 flex justify-between items-center">
                @if($file['isDir'])
                    <!-- Folder -->
                    <span 
                        wire:click="navigateTo('{{ $file['path'] }}')" 
                        class="cursor-pointer hover:underline text-blue-600">
                        📁 {{ $file['name'] }}
                    </span>
                @else
                    <!-- File -->
                    <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" 
                       class="text-gray-800 hover:text-blue-600">
                        📄 {{ $file['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>

</div>
