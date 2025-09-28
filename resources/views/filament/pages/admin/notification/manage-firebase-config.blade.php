<x-filament::page>
    {{ $this->form }}

    <div class="mt-4">
        <x-filament::button
            wire:click="submit"
            color="primary"
            size="md"
            class="ml-auto"
        >
            Save
        </x-filament::button>
    </div>
</x-filament::page>
