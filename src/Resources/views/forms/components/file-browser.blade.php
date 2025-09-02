<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :files="$files"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
    >
        {{-- Interact with the `state` property in Alpine.js --}}
        <template x-if="state">
            <div>
                <p>Selected file: <span x-text="state.name"></span></p>
                <button type="button" @click="state = null">Remove</button>
            </div>
        </template>

        <x-filament::modal>
            <x-slot name="trigger">
                <x-filament::button>
                    Browse
                </x-filament::button>
            </x-slot>

            <div>
                @foreach ($files as $file)
                    <p>{{ $file }}</p>
                @endforeach
            </div>
        </x-filament::modal>
    </div>
</x-dynamic-component>
