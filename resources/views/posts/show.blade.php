@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <article class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            
            <div class="flex items-center justify-between mb-6 pb-6 border-b">
                <p class="text-gray-600">
                    By <span class="font-semibold">{{ $post->user->name }}</span> • 
                    {{ $post->published_at->format('F d, Y') }}
                </p>
                
                @can('update', $post)
                <div class="flex space-x-2">
                    <a href="{{ route('posts.edit', $post) }}" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
                @endcan
            </div>

            <div class="prose max-w-none">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>
    </article>

    <div class="mt-6">
        <a href="{{ route('posts.index') }}" class="text-indigo-600 hover:text-indigo-500">
            ← Back to all posts
        </a>
    </div>
</div>
@endsection