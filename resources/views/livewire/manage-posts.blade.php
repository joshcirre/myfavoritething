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

    public function createPost()
    {
        $this->validate([
            'content' => ['required', 'string', 'max:280'],
            'file' => ['nullable', 'file', 'max:12288'], // 12MB max
        ]);

        $media_url = null;
        if ($this->file) {
            $path = Storage::disk('s3')->put('post-attachments', $this->file);
            $media_url = Storage::disk('s3')->url($path);
        }

        $this->Feed->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'media_url' => $media_url,
        ]);

        $this->reset('content', 'file');
    }

    public function with()
    {
        return [
            'posts' => $this->feed->posts()->with('user')->orderBy('created_at', 'desc')->get(),
        ];
    }
}; ?>

<div class="flex flex-col h-screen">
    <div class="flex overflow-y-auto flex-col-reverse flex-grow p-4 space-y-4 space-y-reverse">
        @foreach ($posts as $post)
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        <div
                            class="flex justify-center items-center w-10 h-10 font-bold text-white bg-indigo-600 rounded-full">
                            {{ strtoupper(substr($post->user->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <h4 class="font-bold text-gray-900">{{ $post->user->name }}</h4>
                            <span class="ml-2 text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-800">{{ $post->content }}</p>
                        @if ($post->media_url)
                            <div class="mt-2">
                                <img src="{{ $post->media_url }}" alt="Post attachment"
                                    class="max-w-full h-auto rounded-lg">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-4 bg-gray-100">
        <form wire:submit.prevent="createPost" class="space-y-4">
            <div>
                <textarea wire:model="content" id="content" name="content" rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    required maxlength="280" placeholder="What's happening?"></textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div class="flex justify-between items-center">
                <input type="file" wire:model="file"
                    class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-primary-button>{{ __('Post') }}</x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </form>
    </div>
</div>
