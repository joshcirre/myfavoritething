<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public Feed $feed;

    public function mount(Feed $feed)
    {
        $this->feed = $feed;
    }

    public function toggleFavorite()
    {
        auth()
            ->user()
            ->favoritedFeeds()
            ->toggle($this->feed->id);
        $this->feed->refresh();
    }

    public function with(): array
    {
        return [
            'isFavorited' => auth()
                ->user()
                ->favoritedFeeds->contains($this->feed->id),
            'canFavorite' => !Gate::allows('manage-feed', $this->feed),
        ];
    }
}; ?>

<x-secondary-button wire:click="toggleFavorite"
    class="{{ $isFavorited ? 'bg-yellow-500 text-white hover:bg-yellow-600' : '' }}">
    {{ $isFavorited ? __('Unfavorite') : __('Favorite') }}
</x-secondary-button>
