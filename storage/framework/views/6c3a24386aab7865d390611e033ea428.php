

<?php $__env->startSection('title', 'Buat Surat Perjalanan Dinas'); ?>
<?php $__env->startSection('page-title', 'Buat Surat Perjalanan Dinas'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('business-trips.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Letter Number -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" id="letter_number" name="letter_number" value="<?php echo e(old('letter_number', $letterNumber)); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['letter_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['letter_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Section -->
                <?php if(auth()->user()->isSuperAdmin()): ?>
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">Seksi <span class="text-red-500">*</span></label>
                    <select id="section_id" name="section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Pilih Seksi</option>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->id); ?>" <?php echo e(old('section_id') == $section->id ? 'selected' : ''); ?>><?php echo e($section->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php else: ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seksi</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                        <?php echo e(auth()->user()->section->name); ?>

                    </div>
                    <input type="hidden" id="section_id" name="section_id" value="<?php echo e(auth()->user()->section_id); ?>">
                </div>
                <?php endif; ?>
            </div>

            <!-- Employee Name -->
            <div class="mb-6">
                <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Pegawai <span class="text-red-500">*</span></label>
                <input type="text" id="employee_name" name="employee_name" value="<?php echo e(old('employee_name')); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['employee_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['employee_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Destination -->
            <div class="mb-6">
                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Tujuan Perjalanan <span class="text-red-500">*</span></label>
                <input type="text" id="destination" name="destination" value="<?php echo e(old('destination')); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['destination'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['destination'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berangkat <span class="text-red-500">*</span></label>
                    <input type="date" id="departure_date" name="departure_date" value="<?php echo e(old('departure_date')); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['departure_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['departure_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="return_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali <span class="text-red-500">*</span></label>
                    <input type="date" id="return_date" name="return_date" value="<?php echo e(old('return_date')); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['return_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['return_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Purpose -->
            <div class="mb-6">
                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Keperluan/Tujuan <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('purpose')); ?></textarea>
                <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Transport & Cost -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="transport" class="block text-sm font-medium text-gray-700 mb-2">Transport <span class="text-red-500">*</span></label>
                    <select id="transport" name="transport" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['transport'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Pilih Transport</option>
                        <option value="Mobil Dinas" <?php echo e(old('transport') == 'Mobil Dinas' ? 'selected' : ''); ?>>Mobil Dinas</option>
                        <option value="Mobil Pribadi" <?php echo e(old('transport') == 'Mobil Pribadi' ? 'selected' : ''); ?>>Mobil Pribadi</option>
                        <option value="Motor" <?php echo e(old('transport') == 'Motor' ? 'selected' : ''); ?>>Motor</option>
                        <option value="Pesawat" <?php echo e(old('transport') == 'Pesawat' ? 'selected' : ''); ?>>Pesawat</option>
                        <option value="Kereta" <?php echo e(old('transport') == 'Kereta' ? 'selected' : ''); ?>>Kereta</option>
                        <option value="Travel" <?php echo e(old('transport') == 'Travel' ? 'selected' : ''); ?>>Travel</option>
                    </select>
                    <?php $__errorArgs = ['transport'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimasi Biaya (Rp)</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" value="<?php echo e(old('estimated_cost')); ?>" min="0" step="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['estimated_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['estimated_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- E-Money Card Usage (Only for Company Vehicle) -->
            <div id="card-usage-section" class="mb-6 border border-indigo-200 rounded-lg p-6 bg-indigo-50" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Penggunaan Kartu E-Money</h3>
                <p class="text-sm text-gray-600 mb-4">Untuk pembayaran tol dan parkir menggunakan mobil dinas</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="card_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kartu</label>
                        <select id="card_id" name="card_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Pilih kartu...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih kartu e-money yang tersedia</p>
                    </div>
                    
                    <div>
                        <label for="card_initial_balance_display" class="block text-sm font-medium text-gray-700 mb-2">Saldo Awal</label>
                        <input type="text" id="card_initial_balance_display" readonly class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        <input type="hidden" id="card_initial_balance" name="card_initial_balance">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="card_usage_amount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pemakaian (Rp)</label>
                        <input type="number" id="card_usage_amount" name="card_usage_amount" min="0" step="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Total tol + parkir</p>
                    </div>
                    
                    <div>
                        <label for="card_final_balance_display" class="block text-sm font-medium text-gray-700 mb-2">Saldo Akhir (Otomatis)</label>
                        <input type="text" id="card_final_balance_display" readonly class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-green-600 font-semibold">
                    </div>
                </div>
                
                <div>
                    <label for="card_usage_notes" class="block text-sm font-medium text-gray-700 mb-2">Rincian Pemakaian</label>
                    <textarea id="card_usage_notes" name="card_usage_notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: Tol Cikampek Rp 50.000, Parkir Mall Rp 10.000"></textarea>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="draft" <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    <option value="approved" <?php echo e(old('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                    <option value="completed" <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Attachment -->
            <div class="mb-6">
                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Lampiran (PDF/Image, max 2MB)</label>
                <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('business-trips.index')); ?>" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Simpan SPD</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo e(asset('js/employee-autocomplete.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeNameInput = document.getElementById('employee_name');
    
    if (employeeNameInput) {
        initEmployeeAutocomplete(employeeNameInput, {
            sectionIdGetter: () => {
                const sectionSelect = document.getElementById('section_id');
                return sectionSelect ? sectionSelect.value : null;
            },
            onSelect: (employee) => {
                console.log('Selected employee:', employee);
            }
        });
    }

    // Card Usage Logic
    const transportSelect = document.getElementById('transport');
    const cardUsageSection = document.getElementById('card-usage-section');
    const cardSelect = document.getElementById('card_id');
    const cardInitialBalance = document.getElementById('card_initial_balance');
    const cardInitialBalanceDisplay = document.getElementById('card_initial_balance_display');
    const cardUsageAmount = document.getElementById('card_usage_amount');
    const cardFinalBalanceDisplay = document.getElementById('card_final_balance_display');
    const sectionSelect = document.getElementById('section_id');

    // Show/hide card section based on transport selection
    transportSelect.addEventListener('change', function() {
        if (this.value === 'Mobil Dinas') {
            cardUsageSection.style.display = 'block';
            loadCards();
        } else {
            cardUsageSection.style.display = 'none';
            resetCardFields();
        }
    });

    // Load cards when section changes (for Super Admin)
    if (sectionSelect) {
        sectionSelect.addEventListener('change', function() {
            if (transportSelect.value === 'Mobil Dinas') {
                loadCards();
            }
        });
    }

    // Update initial balance when card is selected
    cardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const balance = parseFloat(selectedOption.dataset.balance || 0);
            cardInitialBalance.value = balance;
            cardInitialBalanceDisplay.value = formatRupiah(balance);
            calculateFinalBalance();
        } else {
            resetCardFields();
        }
    });

    // Calculate final balance when usage amount changes
    cardUsageAmount.addEventListener('input', calculateFinalBalance);

    // Load available cards
    function loadCards() {
        const sectionId = sectionSelect ? sectionSelect.value : null;
        
        if (!sectionId) {
            cardSelect.innerHTML = '<option value="">Tidak ada kartu tersedia</option>';
            return;
        }

        fetch(`/cards/active?section_id=${sectionId}`)
            .then(response => response.json())
            .then(cards => {
                cardSelect.innerHTML = '<option value="">Pilih kartu...</option>';
                
                if (cards.length === 0) {
                    cardSelect.innerHTML += '<option value="" disabled>Tidak ada kartu tersedia untuk seksi ini</option>';
                } else {
                    cards.forEach(card => {
                        const cardTypeName = getCardTypeName(card.card_type);
                        const balance = formatRupiah(card.current_balance);
                        cardSelect.innerHTML += `<option value="${card.id}" data-balance="${card.current_balance}">${card.card_number} - ${cardTypeName} (${balance})</option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading cards:', error);
                cardSelect.innerHTML = '<option value="">Error loading cards</option>';
            });
    }

    // Calculate final balance
    function calculateFinalBalance() {
        const initial = parseFloat(cardInitialBalance.value || 0);
        const usage = parseFloat(cardUsageAmount.value || 0);
        const final = initial - usage;
        
        cardFinalBalanceDisplay.value = formatRupiah(final);
        
        // Change color based on remaining balance
        if (final < 50000) {
            cardFinalBalanceDisplay.classList.remove('text-green-600');
            cardFinalBalanceDisplay.classList.add('text-red-600');
        } else {
            cardFinalBalanceDisplay.classList.remove('text-red-600');
            cardFinalBalanceDisplay.classList.add('text-green-600');
        }
    }

    // Reset card fields
    function resetCardFields() {
        cardSelect.value = '';
        cardInitialBalance.value = '';
        cardInitialBalanceDisplay.value = '';
        cardUsageAmount.value = '';
        cardFinalBalanceDisplay.value = '';
    }

    // Format rupiah
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // Get card type name
    function getCardTypeName(type) {
        const types = {
            'flazz': 'Flazz (BCA)',
            'brizzi': 'Brizzi (BRI)',
            'e-toll': 'E-Toll (Mandiri)',
            'other': 'Lainnya'
        };
        return types[type] || type;
    }

    // Load cards on page load if Mobil Dinas is selected
    if (transportSelect.value === 'Mobil Dinas') {
        cardUsageSection.style.display = 'block';
        loadCards();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/business-trips/create.blade.php ENDPATH**/ ?>