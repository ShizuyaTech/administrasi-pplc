

<?php $__env->startSection('title', 'Data Overtime'); ?>
<?php $__env->startSection('page-title', 'Data Overtime'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showRejectModal: false, rejectBatchId: null }">
    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                    <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="regular" <?php echo e(request('type') == 'regular' ? 'selected' : ''); ?>>Harian</option>
                    <option value="additional" <?php echo e(request('type') == 'additional' ? 'selected' : ''); ?>>Susulan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Overtime</h2>
        <div class="flex space-x-3">
            <?php if(auth()->user()->hasPermission('export-overtimes')): ?>
            <a href="<?php echo e(route('overtimes.export', request()->query())); ?>" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
            <?php endif; ?>
            <?php if(auth()->user()->hasPermission('create-overtime')): ?>
            <a href="<?php echo e(route('overtimes.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Overtime
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Karyawan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Jam</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php $__empty_1 = true; $__currentLoopData = $overtimeBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php echo e(\Carbon\Carbon::parse($batch->date)->format('d M Y')); ?>

                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php
                                $section = \App\Models\Section::find($batch->section_id);
                            ?>
                            <?php echo e($section ? $section->name : '-'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php echo e(substr($batch->start_time, 0, 5)); ?> - <?php echo e(substr($batch->end_time, 0, 5)); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full font-semibold">
                                <?php echo e($batch->employee_count); ?> orang
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            <?php echo e(number_format($batch->total_hours, 1)); ?> jam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded <?php echo e($batch->type == 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'); ?>">
                                <?php echo e($batch->type == 'regular' ? 'Harian' : 'Susulan'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if($batch->status == 'pending'): ?>
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                            <?php elseif($batch->status == 'approved'): ?>
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex flex-col gap-2 items-center">
                                <!-- Detail Button -->
                                <a href="<?php echo e(route('overtimes.batch.detail', ['batchId' => $batch->batch_id])); ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Detail
                                </a>
                                
                                <?php if($batch->status == 'pending'): ?>
                                    <!-- Edit Button for Leader/Foreman/Admin -->
                                    <?php if(auth()->user()->canApproveOvertimes() || auth()->user()->isLeaderOrForeman() || auth()->user()->canManageAllSections()): ?>
                                        <a href="<?php echo e(route('overtimes.batch.edit', ['batchId' => $batch->batch_id])); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900 font-medium">
                                            Edit
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if(auth()->user()->canApproveOvertimes()): ?>
                                        <div class="flex gap-2">
                                            <form action="<?php echo e(route('overtimes.batch.approve', ['batchId' => $batch->batch_id])); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 font-medium"
                                                        onclick="return confirm('Approve batch overtime untuk <?php echo e($batch->employee_count); ?> karyawan?')">
                                                    Approve
                                                </button>
                                            </form>
                                            <button @click="showRejectModal = true; rejectBatchId = '<?php echo e($batch->batch_id); ?>'" 
                                                    class="text-red-600 hover:text-red-900 font-medium">
                                                Reject
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->hasPermission('delete-overtime')): ?>
                                        <form action="<?php echo e(route('overtimes.batch.delete', ['batchId' => $batch->batch_id])); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 font-medium"
                                                    onclick="return confirm('Hapus batch overtime untuk <?php echo e($batch->employee_count); ?> karyawan?')">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada data overtime</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($overtimeBatches->hasPages()): ?>
        <div class="px-6 py-4 border-t"><?php echo e($overtimeBatches->links()); ?></div>
        <?php endif; ?>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Reject Batch Overtime</h3>
            <form :action="'/overtimes/batch/' + rejectBatchId + '/reject'" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Reject</label>
                    <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/overtimes/index.blade.php ENDPATH**/ ?>