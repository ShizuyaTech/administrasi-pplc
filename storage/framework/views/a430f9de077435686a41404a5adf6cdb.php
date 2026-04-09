

<?php $__env->startSection('title', 'Penugasan Section'); ?>
<?php $__env->startSection('page-title', 'Penugasan Section'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Penugasan Section ke Supervisor & Manager</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola section yang dapat dikelola oleh Supervisor dan Manager</p>
        </div>
        <a href="<?php echo e(route('sections.index')); ?>" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Section
        </a>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
        <p><?php echo e(session('success')); ?></p>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
        <p><?php echo e(session('error')); ?></p>
    </div>
    <?php endif; ?>

    <!-- Section Overview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-md font-semibold text-gray-800 mb-4">📋 Available Sections</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo e($section->name); ?></p>
                        <?php if($section->code): ?>
                        <p class="text-xs text-gray-600 mt-1">Code: <?php echo e($section->code); ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <?php echo e($section->users_count); ?>

                    </span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Supervisors Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
            <h3 class="text-md font-semibold text-gray-800">👨‍💼 Supervisors</h3>
        </div>

        <div class="p-6">
            <?php $__empty_1 = true; $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mb-6 last:mb-0 pb-6 last:pb-0 border-b last:border-b-0 border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="bg-blue-100 rounded-full p-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900"><?php echo e($supervisor->name); ?></p>
                                <p class="text-sm text-gray-600"><?php echo e($supervisor->email); ?></p>
                            </div>
                        </div>

                        <div class="ml-14">
                            <?php if($supervisor->sections->count() > 0): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $supervisor->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                    <span><?php echo e($section->name); ?></span>
                                    <form action="<?php echo e(route('sections.assignments.remove', [$supervisor->id, $section->id])); ?>" 
                                          method="POST" 
                                          class="inline ml-2"
                                          onsubmit="return confirm('Hapus penugasan section ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php else: ?>
                            <p class="text-sm text-gray-500 italic">Belum ada section yang ditugaskan</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="<?php echo e(route('sections.assign.form', $supervisor->id)); ?>" 
                       class="ml-4 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Kelola
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-500 py-4">Tidak ada user dengan role Supervisor</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Managers Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
            <h3 class="text-md font-semibold text-gray-800">👔 Managers</h3>
        </div>

        <div class="p-6">
            <?php $__empty_1 = true; $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mb-6 last:mb-0 pb-6 last:pb-0 border-b last:border-b-0 border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="bg-purple-100 rounded-full p-2">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900"><?php echo e($manager->name); ?></p>
                                <p class="text-sm text-gray-600"><?php echo e($manager->email); ?></p>
                            </div>
                        </div>

                        <div class="ml-14">
                            <?php if($manager->sections->count() > 0): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $manager->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                    <span><?php echo e($section->name); ?></span>
                                    <form action="<?php echo e(route('sections.assignments.remove', [$manager->id, $section->id])); ?>" 
                                          method="POST" 
                                          class="inline ml-2"
                                          onsubmit="return confirm('Hapus penugasan section ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php else: ?>
                            <p class="text-sm text-gray-500 italic">Belum ada section yang ditugaskan</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="<?php echo e(route('sections.assign.form', $manager->id)); ?>" 
                       class="ml-4 px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Kelola
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-500 py-4">Tidak ada user dengan role Manager</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/sections/assignments.blade.php ENDPATH**/ ?>