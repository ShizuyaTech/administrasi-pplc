

<?php $__env->startSection('title', 'Jam Istirahat'); ?>
<?php $__env->startSection('page-title', 'Jam Istirahat'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Jam Istirahat</h2>
        <?php if(auth()->user()->hasPermission('create-break-time')): ?>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('break-times.import.form')); ?>"
               class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </a>
            <a href="<?php echo e(route('break-times.create')); ?>" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Jam Istirahat
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Mulai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Selesai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $breakTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breakTime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900"><?php echo e($breakTime->name); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            <?php echo e(\Carbon\Carbon::parse($breakTime->start_time)->format('H:i')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            <?php echo e(\Carbon\Carbon::parse($breakTime->end_time)->format('H:i')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                            <?php echo e($breakTime->getDurationInMinutes()); ?> menit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if($breakTime->is_active): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Non-Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                            <a href="<?php echo e(route('break-times.edit', $breakTime)); ?>" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                            <form action="<?php echo e(route('break-times.destroy', $breakTime)); ?>" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus jam istirahat ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Belum ada jam istirahat yang didaftarkan</p>
                            <a href="<?php echo e(route('break-times.create')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mt-2 inline-block">
                                Tambah Jam Istirahat Pertama
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($breakTimes->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($breakTimes->links('pagination.custom')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/break-times/index.blade.php ENDPATH**/ ?>