<x-filament-panels::page>
    <form wire:submit.prevent="update" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit">Update</x-filament::button>
    </form>
</x-filament-panels::page>
