<article class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-md">
    <div class="mb-5 flex items-center justify-between text-gray-500">
        <span class="bg-primary-200 text-primary-800 inline-flex items-center rounded px-2.5 py-0.5 text-xs font-medium">
            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z">
                </path>
            </svg>
            Tutorial
        </span>
        <span class="text-sm">{{ $post->created_at->diffForHumans() }}</span>
    </div>
    <h2 class="mb-2 line-clamp-1 text-2xl font-bold tracking-tight text-white"><a href="#">{{ $post->title }}</a></h2>
    <p class="mb-5 min-h-[100px] font-light text-gray-400">{{ $post->content }}</p>
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <img class="h-7 w-7 rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png" alt="Jese Leos avatar" />
            <span class="font-medium text-white">
                {{ $post->user->name }}
            </span>
        </div>
        <a class="inline-flex items-center font-medium text-white hover:underline" href="#">
            Read more
            <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
        </a>
    </div>
</article>
