

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">
            Selamat Datang, <?php echo e(auth()->user()->name); ?>! 👋
        </h2>
        <p class="text-indigo-100">
            <?php if(auth()->user()->section): ?>
                Anda masuk sebagai <span class="font-semibold"><?php echo e(auth()->user()->role->name); ?></span> 
                dari seksi <span class="font-semibold"><?php echo e(auth()->user()->section->name); ?></span>
                <?php if(auth()->user()->shift): ?> - Shift <?php echo e(auth()->user()->shift); ?> <?php endif; ?>
            <?php else: ?>
                Anda masuk sebagai <span class="font-semibold"><?php echo e(auth()->user()->role->name); ?></span>
            <?php endif; ?>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Absensi Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Absensi Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_absences']); ?></h3>
                    <p class="text-xs text-gray-500 mt-1">Total record</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Overtime Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Overtime Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_overtimes']); ?></h3>
                    <p class="text-xs text-yellow-600 mt-1"><?php echo e($stats['pending_overtimes']); ?> pending approval</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: SPD Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">SPD Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_business_trips']); ?></h3>
                    <p class="text-xs text-yellow-600 mt-1"><?php echo e($stats['pending_business_trips']); ?> draft</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Low Stock Items -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 <?php echo e($stats['low_stock_items'] > 0 ? 'border-red-500' : 'border-gray-300'); ?>">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Low Stock Alert</p>
                    <h3 class="text-3xl font-bold <?php echo e($stats['low_stock_items'] > 0 ? 'text-red-600' : 'text-gray-800'); ?>"><?php echo e($stats['low_stock_items']); ?></h3>
                    <p class="text-xs text-gray-500 mt-1">dari <?php echo e($stats['total_consumables']); ?> item</p>
                </div>
                <div class="w-14 h-14 <?php echo e($stats['low_stock_items'] > 0 ? 'bg-red-100' : 'bg-gray-100'); ?> rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 <?php echo e($stats['low_stock_items'] > 0 ? 'text-red-600' : 'text-gray-600'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas 6 Bulan Terakhir</h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⚠️ Item Low Stock</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $lowStockConsumables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800"><?php echo e($item->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($item->section->name); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-red-600"><?php echo e(number_format($item->current_stock, 0)); ?> <?php echo e($item->unit); ?></p>
                            <p class="text-xs text-gray-500">Min: <?php echo e(number_format($item->minimum_stock, 0)); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">Semua item stok aman ✓</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Absences -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Absensi Terbaru</h3>
            </div>
            <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentAbsences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?php echo e($absence->section->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($absence->date->format('d M Y')); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600"><?php echo e($absence->present); ?> hadir</p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 text-sm py-4">Belum ada data</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Overtimes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Overtime Terbaru</h3>
            </div>
            <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentOvertimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?php echo e($overtime->employee_name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($overtime->date->format('d M Y')); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-blue-600"><?php echo e(number_format($overtime->total_hours, 1)); ?>h</p>
                            <span class="text-xs px-2 py-1 rounded <?php echo e($overtime->status == 'approved' ? 'bg-green-100 text-green-800' : ($overtime->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                <?php echo e(ucfirst($overtime->status)); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 text-sm py-4">Belum ada data</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Business Trips -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">SPD Terbaru</h3>
            </div>
            <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentBusinessTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?php echo e($trip->employee_name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($trip->destination); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600"><?php echo e($trip->departure_date->format('d M')); ?></p>
                            <span class="text-xs px-2 py-1 rounded <?php echo e($trip->status == 'approved' ? 'bg-green-100 text-green-800' : ($trip->status == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                                <?php echo e(ucfirst($trip->status)); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 text-sm py-4">Belum ada data</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('monthlyChart');
    const monthlyData = <?php echo json_encode($monthlyData, 15, 512) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [
                {
                    label: 'Absensi',
                    data: monthlyData.map(d => d.absences),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'Overtime',
                    data: monthlyData.map(d => d.overtimes),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'SPD',
                    data: monthlyData.map(d => d.business_trips),
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            Selamat Datang, <?php echo e(auth()->user()->name); ?>! 👋
        </h2>
        <p class="text-gray-600">
            <?php if(auth()->user()->section): ?>
                Anda masuk sebagai <span class="font-semibold"><?php echo e(auth()->user()->role->name); ?></span> 
                dari seksi <span class="font-semibold"><?php echo e(auth()->user()->section->name); ?></span>
                <?php if(auth()->user()->shift): ?>
                    - Shift <?php echo e(auth()->user()->shift); ?>

                <?php endif; ?>
            <?php else: ?>
                Anda masuk sebagai <span class="font-semibold"><?php echo e(auth()->user()->role->name); ?></span>
            <?php endif; ?>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Kehadiran Hari Ini -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Kehadiran Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800">0</h3>
                    <p class="text-xs text-green-600 mt-1">dari 0 member</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Overtime Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Overtime Bulan Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800">0</h3>
                    <p class="text-xs text-blue-600 mt-1">total jam</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Surat Perjalanan Dinas Aktif -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Surat Perjalanan Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-800">0</h3>
                    <p class="text-xs text-purple-600 mt-1">sedang berjalan</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Stok Menipis -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Stok Menipis</p>
                    <h3 class="text-2xl font-bold text-gray-800">0</h3>
                    <p class="text-xs text-red-600 mt-1">item perlu restock</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg transition group">
                <svg class="w-8 h-8 text-gray-600 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Input Absensi</span>
            </a>
            <a href="#" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg transition group">
                <svg class="w-8 h-8 text-gray-600 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Input Overtime</span>
            </a>
            <a href="#" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg transition group">
                <svg class="w-8 h-8 text-gray-600 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Buat SPD</span>
            </a>
            <a href="#" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg transition group">
                <svg class="w-8 h-8 text-gray-600 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Update Stok</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
        </div>
        <div class="p-6">
            <p class="text-center text-gray-500 py-8">No recent activity</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Development\administrasi-app\resources\views/dashboard.blade.php ENDPATH**/ ?>