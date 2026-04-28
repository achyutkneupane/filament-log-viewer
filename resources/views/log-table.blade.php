<x-filament-panels::page>
    <x-filament::tabs>
        @foreach($this->getCachedTabs() as $tab)
            <x-filament::tabs.item
                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"
                :active="$this->tabIsActive($tab->getId())"
                wire:click="$set('activeTab', '{{ $tab->getId() }}')"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    {{ $this->table }}
</x-filament-panels::page>
