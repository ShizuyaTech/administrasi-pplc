

<?php $__env->startSection('title', 'Manajemen Kartu E-Money'); ?>
<?php $__env->startSection('page-title', 'Manajemen Kartu E-Money'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Kartu E-Money</h2>
                <p class="text-sm text-gray-600">Kelola kartu Flazz, Brizzi, dan E-Toll untuk perjalanan dinas</p>
            </div>
            <?php if(auth()->user()->hasPermission('create-card')): ?>
            <div class="flex items-center space-x-2">
                <a href="<?php echo e(route('cards.import.form')); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </a>
                <a href="<?php echo e(route('cards.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Kartu
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Seksi</label>
                <select name="section_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Seksi</option>
                    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($section->id); ?>" <?php echo e(request('section_id') == $section->id ? 'selected' : ''); ?>><?php echo e($section->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kartu</label>
                <select name="card_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tipe</option>
                    <option value="flazz" <?php echo e(request('card_type') == 'flazz' ? 'selected' : ''); ?>>Flazz (BCA)</option>
                    <option value="brizzi" <?php echo e(request('card_type') == 'brizzi' ? 'selected' : ''); ?>>Brizzi (BRI)</option>
                    <option value="e-toll" <?php echo e(request('card_type') == 'e-toll' ? 'selected' : ''); ?>>E-Toll (Mandiri)</option>
                    <option value="other" <?php echo e(request('card_type') == 'other' ? 'selected' : ''); ?>>Lainnya</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Tidak Aktif</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nomor Kartu</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nomor kartu..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Cards List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <?php if($cards->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kartu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seksi</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900"><?php echo e($card->card_number); ?></div>
                            <?php if($card->notes): ?>
                            <div class="text-sm text-gray-500"><?php echo e(Str::limit($card->notes, 30)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($card->card_type == 'flazz'): ?> bg-blue-100 text-blue-800
                                <?php elseif($card->card_type == 'brizzi'): ?> bg-green-100 text-green-800
                                <?php elseif($card->card_type == 'e-toll'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                <?php echo e($card->card_type_name); ?>

                            </span>
                        </td>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($card->section->name); ?>

                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="font-semibold <?php echo e($card->current_balance < 50000 ? 'text-red-600' : 'text-gray-900'); ?>">
                                <?php echo e($card->formatted_balance); ?>

                            </div>
                            <?php if($card->current_balance < 50000): ?>
                            <div class="text-xs text-red-600">Saldo rendah</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if($card->status == 'active'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Tidak Aktif
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('cards.show', $card)); ?>" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <?php if(auth()->user()->hasPermission('edit-card')): ?>
                                <a href="<?php echo e(route('cards.edit', $card)); ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasPermission('delete-card')): ?>
                                <form action="<?php echo e(route('cards.destroy', $card)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kartu ini?')">
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($cards->links('pagination.custom')); ?>

        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada kartu</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kartu e-money pertama.</p>
            <?php if(auth()->user()->hasPermission('create-card')): ?>
            <div class="mt-6">
                <a href="<?php echo e(route('cards.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Kartu
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/cards/index.blade.php ENDPATH**/ ?>