<?php

use Livewire\Volt\Component;
use App\Models\Post;

new class extends Component {
    public $posts;

    public function mount()
    {
        $this->posts = Post::with('user')->take(4)->get();
    }
}; ?>

<div class="mx-auto rounded-md border border-gray-700 bg-gray-900">
    <div class="mx-auto px-4 py-8 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-2">
            @foreach ($posts as $post)
                <x-card-blog :post="$post" />
            @endforeach
        </div>
    </div>
</div>
