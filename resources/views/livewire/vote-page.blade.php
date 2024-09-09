<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use App\Models\Post;

new class extends Component {
    public Feed $feed;
    public $posts;

    public function mount(Feed $feed)
    {
        $this->feed = $feed;
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $this->posts = $this->feed->posts()->with('user')->get();
    }

    public function vote(Post $post)
    {
        $post->increment('votes');
        $this->loadPosts(); // Reload posts to reflect the new vote count
    }
}; ?>

<div>
    <h1 class="mb-4 text-2xl font-bold">Vote for Your Favorite Posts in "{{ $feed->title }}"</h1>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold">{{ $post->user->name }}</p>
                        <p class="text-gray-600">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-600">
                            Votes: {{ $post->votes }}
                        </span>
                        <button wire:click="vote({{ $post->id }})"
                            class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                            Vote
                        </button>
                    </div>
                </div>
                <p class="mt-2">{{ $post->content }}</p>
                @if ($post->media_url)
                    <img src="{{ $post->media_url }}" alt="Post media" class="mt-2 max-w-full h-auto rounded">
                @endif
            </div>
        @endforeach
    </div>
</div>
