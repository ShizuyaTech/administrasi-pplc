<!-- Top Navigation Bar -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 md:px-6">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Page Title -->
        <div class="flex-1 lg:ml-0 ml-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-4">
            <!-- Section Badge (if not Super Admin) -->
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->section && auth()->user()->role->name !== 'Super Admin'): ?>
                <div class="hidden md:flex items-center space-x-2 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-sm font-medium"><?php echo e(auth()->user()->section->name); ?></span>
                    <?php if(auth()->user()->shift): ?>
                    <span class="text-xs px-2 py-0.5 bg-indigo-100 rounded">Shift <?php echo e(auth()->user()->shift); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Notifications -->
            

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative hidden md:block">
                <button @click="open = !open" 
                        class="flex items-center space-x-3 p-2 hover:bg-gray-100 rounded-lg transition">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            <?php if(auth()->guard()->check()): ?>
                            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            <?php endif; ?>
                        </span>
                    </div>
                    
                </button>

                <!-- User Dropdown -->
                
            </div>
        </div>
    </div>
</header>
<?php /**PATH D:\Development\administrasi-app\resources\views/components/navbar.blade.php ENDPATH**/ ?>