<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        <div class="mb-6">
            {{ $this->form }}
        </div>
    </form>
    {{ $this->table }}
</x-filament-panels::page>

