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

    public function updateSlug()
    {
        if (Gate::allows('manage-feed', $this->feed)) {
            $this->validate([
                'newSlug' => 'required|min:3|max:255|unique:feeds,slug,' . $this->feed->id,
            ]);

            $this->feed->update(['slug' => Str::slug($this->newSlug)]);
            $this->isEditing = false;
        }
    }

    public function with(): array
    {
        return [
            'canManage' => Gate::allows('manage-feed', $this->feed),
        ];
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
                        @if ($canManage)
                            @if ($isEditing)
                                <form wire:submit="updateSlug" class="flex items-center">
                                    <x-text-input wire:model="newSlug" type="text" class="mr-2 text-sm" />
                                    <x-primary-button type="submit"
                                        class="text-xs">{{ __('Save') }}</x-primary-button>
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
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if ($canManage)
            <div class="pt-4 border-t border-gray-200">
                <div class="mt-2 space-y-2">
                    <x-secondary-button class="justify-center w-full">
                        {{ __('Edit Feed Details') }}
                    </x-secondary-button>
                    <x-danger-button class="justify-center w-full">
                        {{ __('Delete Feed') }}
                    </x-danger-button>
                </div>
            </div>
        @endif
    </div>
</div>
