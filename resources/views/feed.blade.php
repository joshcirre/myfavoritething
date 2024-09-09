<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $feed->title }}
            </h2>
            @cannot('manage-feed', $feed)
                <livewire:favorite-feed :feed="$feed" />
            @endcannot
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('manage-feed', $feed)
                        <div x-data="{ showManage: false }">
                            <x-secondary-button @click="showManage = !showManage" class="mb-4">
                                {{ __('Toggle Manage Feed') }}
                            </x-secondary-button>

                            <div x-show="showManage">
                                <livewire:manage-feed :feed="$feed" />
                            </div>
                        </div>
                    @endcan

                    <livewire:manage-posts :feed="$feed" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
