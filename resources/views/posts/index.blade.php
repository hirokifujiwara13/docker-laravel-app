@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2">
                    <a href="{{ route('posts.show', $post) }}" class="text-gray-900 hover:text-indigo-600">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-600 text-sm mb-4">
                    By {{ $post->user->name }} • {{ $post->published_at->format('M d, Y') }}
                </p>
                <p class="text-gray-700">
                    {{ Str::limit($post->content, 150) }}
                </p>
                <a href="{{ route('posts.show', $post) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-500">
                    Read more →
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500">No posts available yet.</p>
            @auth
            <a href="{{ route('posts.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-500">
                Create your first post
            </a>
            @endauth
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection