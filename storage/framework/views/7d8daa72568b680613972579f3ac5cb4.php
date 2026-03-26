

<?php $__env->startSection('title', 'Tambah Data Absensi'); ?>
<?php $__env->startSection('page-title', 'Tambah Data Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('absences.store')); ?>">
            <?php echo csrf_field(); ?>

            <!-- Section Selection -->
            <div class="mb-6">
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Seksi <span class="text-red-500">*</span>
                </label>
                <?php if(auth()->user()->canManageAllSections()): ?>
                    <select id="section_id" 
                            name="section_id" 
                            required
                            onchange="updateShiftOptions()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Pilih Seksi</option>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->id); ?>" <?php echo e(old('section_id', $currentSectionId) == $section->id ? 'selected' : ''); ?>>
                                <?php echo e($section->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php else: ?>
                    <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-medium">
                        <?php echo e(auth()->user()->section->name); ?>

                    </div>
                    <input type="hidden" name="section_id" value="<?php echo e(auth()->user()->section_id); ?>">
                <?php endif; ?>
            </div>

            <!-- Shift Selection -->
            <div class="mb-6">
                <label for="shift_select" class="block text-sm font-medium text-gray-700 mb-2">
                    Shift <span class="text-red-500">*</span>
                </label>
                <select id="shift_select" 
                        required
                        onchange="updateEmployeeCount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value=""><?php echo e(auth()->user()->canManageAllSections() ? 'Pilih Seksi terlebih dahulu' : 'Pilih Shift'); ?></option>
                    <?php if(!auth()->user()->canManageAllSections()): ?>
                        <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shiftOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($shiftOption); ?>" <?php echo e(old('shift', $shift) == $shiftOption ? 'selected' : ''); ?>>
                                <?php echo e($shiftOption); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Pilih shift untuk menghitung otomatis jumlah karyawan</p>
            </div>

            <!-- Info Box for Current Selection -->
            <div id="info_box" class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4 hidden">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-indigo-800 font-medium">Informasi Auto-Fill</p>
                        <p id="info_text" class="text-sm text-indigo-700 mt-1"></p>
                    </div>
                </div>
            </div>

            <!-- Date -->
            <div class="mb-6">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="date" 
                       name="date" 
                       value="<?php echo e(old('date', date('Y-m-d'))); ?>" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Attendance Numbers Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Present -->
                <div>
                    <label for="present" class="block text-sm font-medium text-gray-700 mb-2">
                        Hadir <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="present" 
                           name="present" 
                           value="<?php echo e(old('present', 0)); ?>" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['present'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['present'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Sick -->
                <div>
                    <label for="sick" class="block text-sm font-medium text-gray-700 mb-2">
                        Sakit <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="sick" 
                           name="sick" 
                           value="<?php echo e(old('sick', 0)); ?>" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['sick'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['sick'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Permission -->
                <div>
                    <label for="permission" class="block text-sm font-medium text-gray-700 mb-2">
                        Izin <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="permission" 
                           name="permission" 
                           value="<?php echo e(old('permission', 0)); ?>" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['permission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['permission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Leave -->
                <div>
                    <label for="leave" class="block text-sm font-medium text-gray-700 mb-2">
                        Cuti <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="leave" 
                           name="leave" 
                           value="<?php echo e(old('leave', 0)); ?>" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['leave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['leave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Total Members -->
            <div class="mb-6">
                <label for="total_members" class="block text-sm font-medium text-gray-700 mb-2">
                    Total Member <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="total_members" 
                       name="total_members" 
                       value="<?php echo e(old('total_members', 0)); ?>" 
                       min="1"
                       required
                       readonly
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['total_members'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <p class="mt-1 text-sm text-gray-500">Otomatis terhitung dari Hadir + Sakit + Izin + Cuti</p>
                <?php $__errorArgs = ['total_members'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('absences.index')); ?>" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Employee data from server (grouped by section_id and shift)
const employeeData = <?php echo json_encode($employeeData, 15, 512) ?>;
const shifts = <?php echo json_encode($shifts, 15, 512) ?>;
const currentSectionId = "<?php echo e($currentSectionId ?? ''); ?>";
const currentShift = "<?php echo e($shift ?? ''); ?>";

function calculateTotal() {
    const present = parseInt(document.getElementById('present').value) || 0;
    const sick = parseInt(document.getElementById('sick').value) || 0;
    const permission = parseInt(document.getElementById('permission').value) || 0;
    const leave = parseInt(document.getElementById('leave').value) || 0;
    
    const total = present + sick + permission + leave;
    document.getElementById('total_members').value = total;
}

function updateShiftOptions() {
    const sectionId = document.getElementById('section_id').value;
    const shiftSelect = document.getElementById('shift_select');
    
    // Clear current options
    shiftSelect.innerHTML = '<option value="">Pilih Shift</option>';
    
    if (sectionId && employeeData[sectionId]) {
        // Add available shifts for this section
        Object.keys(employeeData[sectionId]).forEach(shift => {
            const option = document.createElement('option');
            option.value = shift;
            option.textContent = shift;
            shiftSelect.appendChild(option);
        });
        
        // Enable shift select
        shiftSelect.disabled = false;
    } else {
        shiftSelect.disabled = true;
    }
    
    // Reset employee count
    document.getElementById('present').value = 0;
    hideInfoBox();
    calculateTotal();
}

function updateEmployeeCount() {
    const sectionId = document.getElementById('section_id') ? document.getElementById('section_id').value : currentSectionId;
    const shift = document.getElementById('shift_select').value;
    
    if (sectionId && shift && employeeData[sectionId] && employeeData[sectionId][shift]) {
        const count = employeeData[sectionId][shift];
        document.getElementById('present').value = count;
        
        // Show info box
        const sectionName = document.getElementById('section_id') ? 
            document.getElementById('section_id').options[document.getElementById('section_id').selectedIndex].text :
            "<?php echo e(auth()->user()->section->name ?? ''); ?>";
        
        document.getElementById('info_text').textContent = 
            `Ditemukan ${count} karyawan aktif di ${sectionName} dengan shift ${shift}. Field "Hadir" telah diisi otomatis.`;
        document.getElementById('info_box').classList.remove('hidden');
        
        calculateTotal();
    } else {
        document.getElementById('present').value = 0;
        hideInfoBox();
        calculateTotal();
    }
}

function hideInfoBox() {
    document.getElementById('info_box').classList.add('hidden');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    <?php if(!auth()->user()->canManageAllSections()): ?>
        // For non-admin users, auto-load if section and shift are set
        if (currentShift) {
            document.getElementById('shift_select').value = currentShift;
            updateEmployeeCount();
        }
    <?php else: ?>
        // For admin users, if section is pre-selected, load shifts
        if (currentSectionId) {
            updateShiftOptions();
        }
    <?php endif; ?>
    
    calculateTotal();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/absences/create.blade.php ENDPATH**/ ?>