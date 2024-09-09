<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public Feed $feed;
    public $newSlug = '';
    public $isEditing = false;

    public function mount(Feed $feed)
    {
        $this->feed = $feed;
        $this->newSlug = $feed->slug;
    }

    public function editSlug()
    {
        $this->isEditing = true;
    }

    public function deleteFeed()
    {
        if (Gate::allows('manage-feed', $this->feed)) {
            $this->feed->delete();
            return redirect()->route('dashboard');
        }
    }

    public function updateSlug()
    {
        if (Gate::allows('manage-feed', $this->feed)) {
            $this->validate([
                'newSlug' => 'required|min:3|max:255|unique:feeds,slug,' . $this->feed->id,
            ]);

            $oldSlug = $this->feed->slug;
            $this->feed->update(['slug' => Str::slug($this->newSlug)]);
            $this->isEditing = false;

            // Redirect to the new slug URL
            if ($oldSlug !== $this->feed->slug) {
                return redirect()->route('feed.show', $this->feed->slug);
            }
        }
    }
}; ?>

<div class="overflow-hidden bg-white rounded-lg shadow-sm">
    <div class="p-6 space-y-6">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Manage Feed') }}</h3>

        <div class="pt-4 border-t border-gray-200">
            <dl class="divide-y divide-gray-200">
                <div class="flex justify-between py-3 text-sm">
                    <dt class="text-gray-500">{{ __('Author') }}</dt>
                    <dd class="text-gray-900">{{ $feed->user->name }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm">
                    <dt class="text-gray-500">{{ __('Created at') }}</dt>
                    <dd class="text-gray-900">{{ $feed->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm">
                    <dt class="text-gray-500">{{ __('Slug') }}</dt>
                    <dd class="text-gray-900">
                        @can('manage-feed', $feed)
                            @if ($isEditing)
                                <form wire:submit="updateSlug" class="flex items-center">
                                    <div class="flex-grow mr-2">
                                        <x-text-input wire:model="newSlug" type="text" class="w-full text-sm" />
                                        <x-input-error :messages="$errors->get('newSlug')" class="mt-2" />
                                    </div>
                                    <x-primary-button type="submit" class="text-xs">{{ __('Save') }}</x-primary-button>
                                    <x-secondary-button wire:click="$set('isEditing', false)"
                                        class="ml-2 text-xs">{{ __('Cancel') }}</x-secondary-button>
                                </form>
                            @else
                                <div class="flex items-center">
                                    <span class="mr-2">{{ $feed->slug }}</span>
                                    <x-secondary-button wire:click="editSlug"
                                        class="text-xs">{{ __('Edit') }}</x-secondary-button>
                                </div>
                            @endif
                        @else
                            {{ $feed->slug }}
                        @endcan
                    </dd>
                </div>
            </dl>
        </div>

        @can('manage-feed', $feed)
            <div class="pt-4 border-t border-gray-200">
                <div class="mt-2 space-y-2">
                    <x-danger-button class="justify-center w-full" wire:click="deleteFeed"
                        wire:confirm="Are you sure you want to delete this feed?">
                        {{ __('Delete Feed') }}
                    </x-danger-button>
                </div>
            </div>
        @endcan
    </div>
</div>
