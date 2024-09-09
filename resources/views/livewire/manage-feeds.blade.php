<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use Illuminate\Support\Str;

new class extends Component {
    public $title = '';
    public $slug = '';
    public $showSlug = false;
    public $daysActive = 30;

    public function with(): array
    {
        $user = auth()->user();
        $userFeeds = $user->feeds()->with('user')->get();
        $favoritedFeeds = $user->favoritedFeeds()->with('user')->get();

        $allFeeds = $userFeeds->merge($favoritedFeeds)->unique('id');

        return [
            'feeds' => $allFeeds,
            'userFeeds' => $user->feeds->pluck('id'),
            'favoritedFeeds' => $user->favoritedFeeds->pluck('id'),
        ];
    }

    public function createFeed()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|min:3|max:255|unique:feeds',
            'daysActive' => 'required|integer|min:1',
        ]);

        $feedData = [
            'title' => $this->title,
            'days_active' => $this->daysActive,
        ];

        if (!empty($this->slug)) {
            $feedData['slug'] = Str::slug($this->slug);
        }

        auth()->user()->feeds()->create($feedData);

        $this->reset(['title', 'slug', 'showSlug', 'daysActive']);
    }
}; ?>

<div>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('Manage Feeds') }}</h2>

        <!-- Create Feed Form -->
        <form wire:submit="createFeed" class="space-y-4">
            <div>
                <x-input-label for="title" :value="__('Feed Title')" />
                <x-text-input wire:model="title" id="title" type="text" class="block mt-1 w-full" required
                    autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div x-data="{ showSlug: @entangle('showSlug') }">
                <div class="flex items-center">
                    <x-input-label for="showSlugCheckbox" :value="__('Custom Slug')" />
                    <input type="checkbox" id="showSlugCheckbox" x-model="showSlug" class="ml-2">
                </div>

                <div x-show="showSlug" class="mt-2">
                    <x-input-label for="slug" :value="__('Slug')" />
                    <x-text-input wire:model="slug" id="slug" type="text" class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="daysActive" :value="__('Days Active')" />
                <x-text-input wire:model="daysActive" id="daysActive" type="number" class="block mt-1 w-full"
                    required />
                <x-input-error :messages="$errors->get('daysActive')" class="mt-2" />
            </div>

            <div>
                <x-primary-button type="submit">
                    {{ __('Create Feed') }}
                </x-primary-button>
            </div>
        </form>

        <!-- List of All Feeds -->
        <div class="mt-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900">{{ __('Your Feeds') }}</h3>
            @if ($feeds->isEmpty())
                <p class="text-gray-500">{{ __('You have no feeds yet.') }}</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($feeds as $feed)
                        <li
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center py-4 @can('manage-feed', $feed) bg-indigo-50 border-l-4 border-indigo-500 @endcan">
                            <div class="flex flex-row items-center">
                                <span class="ml-4 text-lg font-medium text-gray-900">{{ $feed->title }}</span>
                                @if ($favoritedFeeds->contains($feed->id))
                                    <span class="ml-2 text-yellow-500">★</span>
                                @endif
                                @can('manage-feed', $feed)
                                    <span class="ml-2 text-xs font-semibold text-indigo-600">{{ __('(Your Feed)') }}</span>
                                @endcan
                            </div>
                            <div class="flex flex-col items-start mt-2 sm:flex-row sm:items-center sm:mt-0">
                                <span class="ml-4 text-sm text-gray-500 sm:mr-4">Created by
                                    {{ $feed->user->name }}</span>
                                <a href="{{ $feed->slug ? route('feed.show', $feed->slug) : route('feed.show', $feed->id) }}"
                                    class="inline-flex items-center px-3 py-2 mt-2 ml-4 text-sm font-medium leading-4 text-white bg-indigo-600 rounded-md border border-transparent sm:mt-0 sm:ml-0 sm:mr-2 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('View') }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
