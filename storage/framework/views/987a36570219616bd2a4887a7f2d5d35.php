<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <article class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($post->title); ?></h1>
            
            <div class="flex items-center justify-between mb-6 pb-6 border-b">
                <p class="text-gray-600">
                    By <span class="font-semibold"><?php echo e($post->user->name); ?></span> • 
                    <?php echo e($post->published_at->format('F d, Y')); ?>

                </p>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $post)): ?>
                <div class="flex space-x-2">
                    <a href="<?php echo e(route('posts.edit', $post)); ?>" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Edit
                    </a>
                    <form method="POST" action="<?php echo e(route('posts.destroy', $post)); ?>" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <div class="prose max-w-none">
                <?php echo nl2br(e($post->content)); ?>

            </div>
        </div>
    </article>

    <div class="mt-6">
        <a href="<?php echo e(route('posts.index')); ?>" class="text-indigo-600 hover:text-indigo-500">
            ← Back to all posts
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/posts/show.blade.php ENDPATH**/ ?>