<div 
    x-data="{
        // Initial value from DB
        selected: @js($getState()) || '',
        // Livewire-entangled model
        wireModel: @entangle($getStatePath()),

        files: [],
        currentDir: '',
        breadcrumbs: [],

        fetchFiles(dir = '') {
            this.currentDir = dir;
            fetch('/file-browser-files', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ dir: dir })
            })
            .then(res => res.json())
            .then(data => this.files = data);

            // Update breadcrumbs
            this.breadcrumbs = dir.split('/').filter(Boolean);
        },

        triggerUpload() {
            this.$refs.fileInput.click();
        },

        uploadFile(event) {
            let file = event.target.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('dir', this.currentDir);

            fetch('/file-browser-upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: formData
            }).then(() => {
                event.target.value = '';
                this.fetchFiles(this.currentDir);
            });
        },

        goUp() {
            if (!this.currentDir) return;
            let parts = this.currentDir.split('/');
            parts.pop();
            this.fetchFiles(parts.join('/'));
        },

        navigateBreadcrumb(index) {
            let parts = this.currentDir.split('/');
            let newDir = parts.slice(0, index + 1).join('/');
            this.fetchFiles(newDir);
        }
    }" 
    x-init="fetchFiles()"
>
    <!-- File input textbox and buttons -->
    <div class="flex gap-2 items-center mb-2">
        <x-filament::input 
            type="text" 
            x-model="wireModel"
            class="filament-forms-input w-full" 
            placeholder="Browse to select a file…" 
            readonly 
        />

        <!-- Browse modal trigger -->
        <x-filament::button 
            size="sm" 
            x-on:click="$dispatch('open-modal', { id: '{{ $getId() }}-modal' })"
        >
            Browse
        </x-filament::button>

        <!-- Clear button -->
        <x-filament::button 
            size="sm" 
            color="secondary" 
            x-on:click="selected = ''; wireModel = ''"
        >
            Clear
        </x-filament::button>
    </div>

    <!-- Modal -->
    <x-filament::modal id="{{ $getId() }}-modal" width="lg">
        <x-slot name="heading">
            Select File
        </x-slot>

        <!-- Selected file display -->
        <div class="mb-3 text-gray-700">
            <strong>Selected file:</strong>
            <span x-text="wireModel ? wireModel : 'None'"></span>
        </div>

        <!-- Breadcrumb -->
        <div class="flex flex-wrap gap-1 mb-2">
            <span 
                class="text-blue-600 cursor-pointer hover:underline" 
                x-on:click="fetchFiles('')"
            >
                Home
            </span>
            <template x-for="(crumb, index) in breadcrumbs" :key="index">
                <span class="flex items-center gap-1">
                    <span>/</span>
                    <span 
                        class="text-blue-600 cursor-pointer hover:underline" 
                        x-text="crumb" 
                        x-on:click="navigateBreadcrumb(index)"
                    ></span>
                </span>
            </template>
        </div>

        <!-- Buttons: Upload, New Folder, Up -->
        <div class="mb-4 flex gap-2 items-center">
            <!-- Upload -->
            <x-filament::button size="sm" color="success" x-on:click="triggerUpload()">
                ⬆ Upload File
            </x-filament::button>
            <input 
                type="file" 
                x-ref="fileInput" 
                style="display:none;" 
                x-on:change="uploadFile($event)"
            >

            <!-- New Folder -->
            <x-filament::button size="sm" color="primary" x-on:click="
                let folderName = prompt('Enter new folder name:');
                if(folderName){
                    fetch('/file-browser-create-folder', {
                        method:'POST',
                        headers:{
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type':'application/json'
                        },
                        body: JSON.stringify({ dir: currentDir, folder: folderName })
                    }).then(()=>fetchFiles(currentDir))
                }
            ">
                📁 New Folder
            </x-filament::button>

            <!-- Up button -->
            <x-filament::button size="sm" x-show="currentDir" x-on:click="goUp()">
                ⬆ Up
            </x-filament::button>
        </div>

        <!-- File list -->
        <ul class="divide-y border-t border-b">
            <template x-for="file in files" :key="file.path">
                <li class="p-2 hover:bg-gray-100 flex justify-between items-center">
                    <!-- File/Folder clickable -->
                    <span 
                        x-text="file.isDir ? '📁 ' + file.name : '📄 ' + file.name"
                        :class="file.isDir ? 'text-blue-600 cursor-pointer hover:underline' : 'text-gray-800'"
                        x-on:click="
                            if(file.isDir) { 
                                fetchFiles(file.path) 
                            } else { 
                                selected = file.path; 
                                wireModel = file.path; 
                                $dispatch('close-modal', { id: '{{ $getId() }}-modal' }) 
                            }
                        "
                    ></span>

                    <!-- Inline delete -->
                    <span 
                        class="ml-2 text-red-500 cursor-pointer hover:text-red-700"
                        x-on:click.stop="
                            if(confirm('Are you sure you want to delete ' + file.name + '?')){
                                fetch('/file-browser-delete', {
                                    method:'POST',
                                    headers:{
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Content-Type':'application/json'
                                    },
                                    body: JSON.stringify({ path:file.path, isDir:file.isDir })
                                }).then(()=>fetchFiles(currentDir))
                            }
                        "
                    >
                        <x-filament::icon name="heroicon-o-x-mark" class="h-5 w-5"/>
                    </span>
                </li>
            </template>
        </ul>
    </x-filament::modal>
</div>
