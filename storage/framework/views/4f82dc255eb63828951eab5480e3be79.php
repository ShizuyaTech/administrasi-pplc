<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
     style="display: none;"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed lg:static inset-y-0 left-0 w-64 bg-white shadow-lg z-50 flex flex-col transition-transform duration-300 ease-in-out">
    
    <!-- Logo Section -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <img src="<?php echo e(asset('images/LOGO-IPPI.jpg')); ?>" 
                 alt="IPAI Logo" 
                 class="h-10 w-auto"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <!-- Fallback logo if image not found -->
            <div class="hidden w-10 h-10 bg-indigo-600 rounded-lg items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="text-lg font-bold text-gray-800">PPLC IPPI</span>
        </div>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 scrollbar-hide">
        <div class="space-y-1">
            <!-- Dashboard -->
            <?php if(auth()->user()->hasPermission('view-dashboard')): ?>
            <a href="<?php echo e(route('dashboard')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <?php endif; ?>

            <!-- Absensi -->
            <?php if(auth()->user()->hasPermission('view-absences')): ?>
            <a href="<?php echo e(route('absences.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('absences.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="font-medium">Absensi</span>
            </a>
            <?php endif; ?>

            <!-- Overtime -->
            <?php if(auth()->user()->hasPermission('view-overtimes')): ?>
            <a href="<?php echo e(route('overtimes.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('overtimes.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Overtime</span>
            </a>
            <?php endif; ?>

            <!-- Surat Perjalanan Dinas -->
            <?php if(auth()->user()->hasPermission('view-business-trips')): ?>
            <a href="<?php echo e(route('business-trips.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('business-trips.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">Surat Perjalanan Dinas</span>
            </a>
            <?php endif; ?>

            <!-- Divider - Master Data -->
            <?php
                $canViewEmployees = auth()->user()->hasPermission('view-employees');
                $canViewConsumables = auth()->user()->hasPermission('view-consumables');
                $canViewBreakTimes = auth()->user()->hasPermission('view-break-times');
                $canViewCards = auth()->user()->hasPermission('view-card');
                $hasMasterDataAccess = $canViewEmployees || $canViewConsumables || $canViewBreakTimes || $canViewCards;
            ?>
            
            <?php if($hasMasterDataAccess): ?>
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</p>
            </div>

            <!-- Data Karyawan -->
            <?php if($canViewEmployees): ?>
            <a href="<?php echo e(route('employees.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium">Data Karyawan</span>
            </a>
            <?php endif; ?>

            <!-- Data Consumable -->
            <?php if($canViewConsumables): ?>
            <a href="<?php echo e(route('consumables.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('consumables.*') || request()->routeIs('stock-movements.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium">Data Consumable</span>
            </a>
            <?php endif; ?>

            <!-- Jam Istirahat -->
            <?php if($canViewBreakTimes): ?>
            <a href="<?php echo e(route('break-times.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('break-times.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Jam Istirahat</span>
            </a>
            <?php endif; ?>

            <!-- Kartu E-Money -->
            <?php if($canViewCards): ?>
            <a href="<?php echo e(route('cards.index')); ?>" 
               class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('cards.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="font-medium">Kartu E-Money</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- E-Signature (for Supervisor/Manager) -->
            <?php if(auth()->user()->isSupervisor() || auth()->user()->isManager() || auth()->user()->canManageAllSections()): ?>
            <div class="pt-4 mt-4 border-t border-gray-200">
                <a href="<?php echo e(route('profile.signature')); ?>" 
                   class="flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('profile.signature') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span class="font-medium">Upload E-Signature</span>
                    <?php if(!auth()->user()->hasSignature()): ?>
                    <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">!</span>
                    <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Divider - Settings -->
            <?php
                $canViewUsers = auth()->user()->hasPermission('view-users');
                $canViewRoles = auth()->user()->hasPermission('view-roles');
                $canViewPermissions = auth()->user()->hasPermission('view-permissions');
                $hasSettingsAccess = $canViewUsers || $canViewRoles || $canViewPermissions;
            ?>
            
            <?php if($hasSettingsAccess): ?>
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
            </div>

            <!-- Manage User Dropdown -->
            <div x-data="{ open: <?php echo e(request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('sections.*') ? 'true' : 'false'); ?> }">
                <!-- Dropdown Trigger -->
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('sections.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="font-medium">Manage User</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="mt-1 ml-8 space-y-1">
                    <!-- Manajemen User -->
                    <?php if($canViewUsers): ?>
                    <a href="<?php echo e(route('users.index')); ?>" 
                       class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Manajemen User</span>
                    </a>
                    <?php endif; ?>

                    <!-- Manajemen Role -->
                    <?php if($canViewRoles): ?>
                    <a href="<?php echo e(route('roles.index')); ?>" 
                       class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('roles.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Manajemen Role</span>
                    </a>
                    <?php endif; ?>

                    <!-- Manajemen Permission -->
                    <?php if($canViewPermissions): ?>
                    <a href="<?php echo e(route('permissions.index')); ?>" 
                       class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('permissions.*') && !request()->routeIs('sections.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Manajemen Permission</span>
                    </a>
                    <?php endif; ?>

                    <!-- Manajemen Section -->
                    <?php if(auth()->user()->isSuperAdmin()): ?>
                    <a href="<?php echo e(route('sections.index')); ?>" 
                       class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition <?php echo e(request()->routeIs('sections.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Manajemen Section</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- User Profile Section -->
    <div class="border-t border-gray-200 p-4 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <?php if(auth()->guard()->check()): ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs text-gray-500 truncate"><?php echo e(auth()->user()->role->name ?? 'User'); ?></p>
                <?php if(auth()->user()->section): ?>
                <p class="text-xs text-gray-400 truncate"><?php echo e(auth()->user()->section->name); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-3">
            <?php echo csrf_field(); ?>
            <button type="submit" 
                    class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH D:\Development\administrasi-app\resources\views/components/sidebar.blade.php ENDPATH**/ ?>