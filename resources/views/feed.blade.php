<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between items-center space-y-2 sm:flex-row sm:space-y-0">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $feed->title }}
            </h2>
            @if ($feed->canAccess(auth()->user()))
                @cannot('manage-feed', $feed)
                    <livewire:favorite-feed :feed="$feed" />
                @endcannot
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900 sm:p-6">
                    @if ($feed->canAccess(auth()->user()))
                        @can('manage-feed', $feed)
                            <div x-data="{ showManage: false }" x-cloak class="mb-4">
                                <x-secondary-button @click="showManage = !showManage" class="w-full sm:w-auto">
                                    {{ __('Toggle Manage Feed') }}
                                </x-secondary-button>

                                <div x-show="showManage" class="mt-4">
                                    <livewire:manage-feed :feed="$feed" />
                                </div>
                            </div>
                        @endcan

                        <div class="h-[calc(100vh-16rem)]"> <!-- Adjust this value as needed -->
                            <livewire:manage-posts :feed="$feed" />
                        </div>
                    @else
                        <p>This feed is no longer active.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
