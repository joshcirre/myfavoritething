<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use Illuminate\Support\Str;

new class extends Component {
    public $title = '';
    public $slug = '';
    public $showSlug = false;

    public function with(): array
    {
        return [
            'feeds' => Feed::with('user')->get(),
            'userFeeds' => auth()->user()->feeds->pluck('id'),
            'favoritedFeeds' => auth()->user()->favoritedFeeds->pluck('id'),
        ];
    }

    public function createFeed()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|min:3|max:255|unique:feeds',
        ]);

        $feedData = [
            'title' => $this->title,
            'slug' => !empty($this->slug) ? Str::slug($this->slug) : Str::slug($this->title),
        ];

        auth()->user()->feeds()->create($feedData);

        $this->reset(['title', 'slug', 'showSlug']);
    }
}; ?>

<div class="overflow-hidden bg-white rounded-lg shadow-sm">
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
                <x-primary-button type="submit">
                    {{ __('Create Feed') }}
                </x-primary-button>
            </div>
        </form>

        <!-- List of All Feeds -->
        <div class="mt-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900">{{ __('All Feeds') }}</h3>
            @if ($feeds->isEmpty())
                <p class="text-gray-500">{{ __('There are no feeds yet.') }}</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($feeds as $feed)
                        <li class="flex justify-between items-center py-4">
                            <div class="flex items-center">
                                <span class="text-lg font-medium text-gray-900">{{ $feed->title }}</span>
                                @if ($userFeeds->contains($feed->id))
                                    <span
                                        class="inline-flex px-2 ml-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                        {{ __('Author') }}
                                    </span>
                                @endif
                                @if ($favoritedFeeds->contains($feed->id))
                                    <span class="ml-2 text-yellow-500">★</span>
                                @endif
                            </div>
                            <div class="flex items-center">
                                <span class="mr-4 text-sm text-gray-500">{{ $feed->user->name }}</span>
                                <a href="{{ route('feed.show', $feed) }}"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-indigo-600 rounded-md border border-transparent hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
