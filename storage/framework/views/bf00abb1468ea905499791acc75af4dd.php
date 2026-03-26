

<?php $__env->startSection('title', 'Manage Permissions - ' . $role->name); ?>
<?php $__env->startSection('page-title', 'Manage Permissions untuk Role: ' . $role->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900"><?php echo e($role->name); ?></h3>
                    <p class="text-sm text-gray-600 mt-1"><?php echo e($role->description); ?></p>
                    <p class="text-xs text-gray-500 mt-2">
                        Saat ini memiliki <span class="font-semibold"><?php echo e($role->permissions->count()); ?></span> permissions
                    </p>
                </div>
                <a href="<?php echo e(route('roles.index')); ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Kembali
                </a>
            </div>
        </div>

        <form action="<?php echo e(route('roles.permissions.update', $role)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php if($permissionGroups->count() > 0): ?>
                <div class="space-y-6">
                    <?php $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-200 rounded-lg p-5">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                <?php echo e($group ? ucfirst($group) : 'General'); ?>

                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="<?php echo e($permission->id); ?>"
                                               <?php echo e($role->permissions->contains($permission->id) ? 'checked' : ''); ?>

                                               class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-900 block"><?php echo e($permission->name); ?></span>
                                            <span class="text-xs text-gray-500 block mt-0.5"><?php echo e($permission->slug); ?></span>
                                            <?php if($permission->description): ?>
                                                <span class="text-xs text-gray-400 block mt-1"><?php echo e($permission->description); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <strong>Note:</strong> Pilih permissions yang ingin diberikan ke role ini
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="uncheckAll()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Hapus Semua
                        </button>
                        <button type="button" 
                                onclick="checkAll()"
                                class="px-4 py-2 border border-indigo-300 text-indigo-700 rounded-lg hover:bg-indigo-50 transition">
                            Pilih Semua
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Permissions
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Belum ada permissions yang tersedia</p>
                    <p class="mt-2">
                        <a href="<?php echo e(route('permissions.create')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Buat Permission Baru
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function checkAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function uncheckAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/roles/permissions.blade.php ENDPATH**/ ?>