<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2">
                    <a href="<?php echo e(route('posts.show', $post)); ?>" class="text-gray-900 hover:text-indigo-600">
                        <?php echo e($post->title); ?>

                    </a>
                </h2>
                <p class="text-gray-600 text-sm mb-4">
                    By <?php echo e($post->user->name); ?> • <?php echo e($post->published_at->format('M d, Y')); ?>

                </p>
                <p class="text-gray-700">
                    <?php echo e(Str::limit($post->content, 150)); ?>

                </p>
                <a href="<?php echo e(route('posts.show', $post)); ?>" class="inline-block mt-4 text-indigo-600 hover:text-indigo-500">
                    Read more →
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500">No posts available yet.</p>
            <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('posts.create')); ?>" class="mt-4 inline-block text-indigo-600 hover:text-indigo-500">
                Create your first post
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-6">
        <?php echo e($posts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/posts/index.blade.php ENDPATH**/ ?>