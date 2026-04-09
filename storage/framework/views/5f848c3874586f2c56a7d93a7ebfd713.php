

<?php $__env->startSection('title', 'Stok Consumable'); ?>
<?php $__env->startSection('page-title', 'Stok Consumable'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <div class="flex flex-wrap gap-3">
        

        <?php if(auth()->user()->hasPermission('create-consumable')): ?>
        <a href="<?php echo e(route('consumables.import.form')); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Excel
        </a>
        <?php endif; ?>
        
        <?php if(auth()->user()->hasPermission('create-stock-movement')): ?>
        <a href="<?php echo e(route('stock-movements.create')); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            Stock In/Out
        </a>
        <?php endif; ?>
        
        
        <a href="<?php echo e(route('consumables.master-items')); ?>" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            List Item Consumable
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="<?php echo e(route('consumables.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Item</label>
            <input type="text" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <?php if(auth()->user()->isSuperAdmin()): ?>
        <div>
            <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Seksi</label>
            <select id="section_id" name="section_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Seksi</option>
                <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($section->id); ?>" <?php echo e(request('section_id') == $section->id ? 'selected' : ''); ?>><?php echo e($section->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>
        
        <div>
            <label for="low_stock" class="block text-sm font-medium text-gray-700 mb-1">Filter Stok</label>
            <select id="low_stock" name="low_stock" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua</option>
                <option value="1" <?php echo e(request('low_stock') == '1' ? 'selected' : ''); ?>>Low Stock Only</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seksi</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Stok</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $consumables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="<?php echo e($item->current_stock <= $item->minimum_stock ? 'bg-red-50' : ''); ?>">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($item->name); ?></div>
                            <div class="text-xs text-gray-500">Unit: <?php echo e($item->unit); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->section->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            <?php echo e(number_format($item->current_stock, 0, ',', '.')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            <?php echo e(number_format($item->minimum_stock, 0, ',', '.')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if($item->current_stock <= $item->minimum_stock): ?>
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-medium">⚠️ Low Stock</span>
                            <?php elseif($item->current_stock <= $item->minimum_stock * 1.5): ?>
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 font-medium">⚡ Warning</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">✓ OK</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="<?php echo e(route('consumables.show', $item)); ?>" class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <?php if(auth()->user()->hasPermission('edit-consumable')): ?>
                                    <a href="<?php echo e(route('consumables.edit', $item)); ?>" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasPermission('delete-consumable')): ?>
                                    <form action="<?php echo e(route('consumables.destroy', $item)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus item ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data consumable.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($consumables->hasPages()): ?>
    <div class="px-6 py-4 border-t border-gray-200">
        <?php echo e($consumables->links('pagination.custom')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/consumables/index.blade.php ENDPATH**/ ?>