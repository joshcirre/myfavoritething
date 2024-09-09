<?php

use Livewire\Volt\Component;
use App\Models\Feed;
use App\Models\Post;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $file;
    public Feed $feed;
    public string $content = '';
    public $posts;

    public function mount()
    {
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $this->posts = $this->feed->posts()->with('user')->orderBy('created_at', 'asc')->get();
    }

    public function createPost()
    {
        $this->validate([
            'content' => ['required', 'string', 'max:280'],
            'file' => ['nullable', 'file'],
        ]);

        $media_url = null;

        if ($this->file) {
            $path = Storage::disk('s3')->put('post-files', $this->file);
            $media_url = Storage::disk('s3')->url($path);
        }

        $post = $this->feed->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'media_url' => $media_url,
        ]);

        // Ensure the media_url is set correctly
        if ($media_url && $post) {
            $post->update(['media_url' => $media_url]);
        }

        $this->reset('content', 'file');
        $this->loadPosts(); // Reload posts after creating a new one
        $this->dispatch('newPost');
    }

    private function isImage($url)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }

    public function deletePost(Post $post)
    {
        if (auth()->user()->can('delete', $post)) {
            $post->delete();
            $this->loadPosts(); // Reload posts after deleting one
        }
    }
}; ?>

<div wire:poll.5s='loadPosts' x-data="{
    scrollToBottom() {
        const container = this.$refs.postsContainer;
        container.scrollTop = container.scrollHeight;
    }
}" x-init="scrollToBottom" @newPost.window="scrollToBottom"
    class="flex flex-col h-full">
    <div x-ref="postsContainer" class="overflow-y-auto flex-grow pr-4 pb-4 mb-4 space-y-4">
        @foreach ($posts as $post)
            <div class="relative p-4 mb-4 rounded-lg shadow bg-slate-50">
                @can('delete', $post)
                    <button wire:click="deletePost({{ $post->id }})"
                        wire:confirm="Are you sure you want to delete this post?"
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700" title="Delete post">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                @endcan
                <div class="flex flex-col items-start sm:flex-row">
                    <div class="flex-shrink-0 mb-2 sm:mb-0 sm:mr-4">
                        <div
                            class="flex justify-center items-center w-10 h-10 font-bold text-white bg-indigo-600 rounded-full">
                            {{ strtoupper(substr($post->user->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex flex-col mb-1 sm:flex-row sm:items-center">
                            <h4 class="font-bold text-gray-900">{{ $post->user->name }}</h4>
                            <span class="text-sm text-gray-500 sm:ml-2">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-800 break-words">{{ $post->content }}</p>
                        @if ($post->media_url)
                            <div class="mt-2">
                                @if ($this->isImage($post->media_url))
                                    <img src="{{ $post->media_url }}" alt="Post image"
                                        class="max-w-full h-auto rounded-lg">
                                @else
                                    <video controls class="max-w-full h-auto rounded-lg">
                                        <source src="{{ $post->media_url }}" type="video/mp4">
                                        <source src="{{ $post->media_url }}" type="video/quicktime">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="sticky bottom-0 p-4 bg-gray-100 rounded-lg">
        <form wire:submit="createPost" class="space-y-4">
            <div>
                <textarea wire:model="content" id="content" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    required maxlength="280" placeholder="Let's remember the thing..."></textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div class="flex flex-col justify-between items-center space-y-2 sm:flex-row sm:space-y-0">
                <input type="file" wire:model="file"
                    class="w-full text-sm text-gray-500 sm:w-auto file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-primary-button type="submit" class="w-full sm:w-auto">
                    <span wire:loading.remove>{{ __('Share Your Thing') }}</span>
                    <span wire:loading>{{ __('Posting...') }}</span>
                </x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </form>
    </div>
</div>
