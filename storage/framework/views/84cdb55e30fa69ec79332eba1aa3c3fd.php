<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center py-16">
        <h1 class="text-5xl font-bold text-gray-900 mb-4">Welcome to Laravel Blog</h1>
        <p class="text-xl text-gray-600 mb-8">A simple blog application built with Laravel and Docker</p>
        
        <div class="space-x-4">
            <a href="<?php echo e(route('posts.index')); ?>" class="inline-block px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700">
                Browse Posts
            </a>
            <?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('register')); ?>" class="inline-block px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300">
                Get Started
            </a>
            <?php else: ?>
            <a href="<?php echo e(route('posts.create')); ?>" class="inline-block px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300">
                Create Post
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="py-12">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Features</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">User Authentication</h3>
                <p class="text-gray-600">Secure registration and login system</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Blog Management</h3>
                <p class="text-gray-600">Create, edit, and delete blog posts</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Docker Ready</h3>
                <p class="text-gray-600">Containerized for easy deployment</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/welcome.blade.php ENDPATH**/ ?>