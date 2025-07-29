<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);
const darkMode = ref(false);
const dropdownOpen = ref(false);
const vendorsDropdownOpen = ref(false);
const accountsPayableDropdownOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle('dark');
};

const closeDropdown = (e) => {
    if (!e.target.closest('.user-dropdown')) {
        dropdownOpen.value = false;
    }
    if (!e.target.closest('.vendors-dropdown')) {
        vendorsDropdownOpen.value = false;
    }
    if (!e.target.closest('.accounts-payable-dropdown')) {
        accountsPayableDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});
</script>

<template>
    <div :class="{ 'dark': darkMode }" class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="lg:flex">
            <!-- Sidebar extending to top -->
            <aside 
                :class="{ 
                    '-translate-x-full': !sidebarOpen,
                    'translate-x-0': sidebarOpen 
                }"
                class="sidebar fixed top-0 left-0 z-50 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 lg:translate-x-0 dark:border-gray-800 dark:bg-gray-900"
            >
            <!-- Logo -->
            <div class="flex items-center justify-between py-6">
                <Link :href="route('dashboard')" class="flex items-center">
                    <img src="/assets/images/ays_translucent.png" alt="AYS Logo" class="h-10 w-auto mr-3">
                    <span class="text-xl font-bold text-gray-800 dark:text-white">CRM Dispatch</span>
                </Link>
                <button 
                    @click="toggleSidebar"
                    class="lg:hidden rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu Label -->
            <div class="px-4 pb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">MENU</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 space-y-2">
                <!-- Dashboard -->
                <Link 
                    :href="route('dashboard')"
                    :class="{ 
                        'bg-blue-50 border-r-4 border-blue-500 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300': route().current('dashboard'),
                        'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800': !route().current('dashboard')
                    }"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium transition-colors"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0M8 5a2 2 0 000 4h8a2 2 0 000-4v0"></path>
                    </svg>
                    Dashboard
                </Link>

                <!-- Customers -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Customers
                </a>

                <!-- Sites -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Sites
                </a>

                <!-- Employees -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Employees
                </a>

                <!-- Vendors -->
                <div class="vendors-dropdown relative">
                    <button 
                        @click="vendorsDropdownOpen = !vendorsDropdownOpen"
                        class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Vendors
                        </div>
                        <svg 
                            :class="{ 'rotate-180': vendorsDropdownOpen }"
                            class="h-4 w-4 transition-transform duration-200"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div 
                        v-show="vendorsDropdownOpen"
                        class="mt-2 ml-8 space-y-1"
                    >
                        <a 
                            href="#"
                            class="block rounded-lg px-4 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                        >
                            Purchase Orders
                        </a>
                        
                        <!-- Accounts Payable with submenu -->
                        <div class="accounts-payable-dropdown relative">
                            <button 
                                @click="accountsPayableDropdownOpen = !accountsPayableDropdownOpen"
                                class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                            >
                                <span>Accounts Payable</span>
                                <svg 
                                    :class="{ 'rotate-180': accountsPayableDropdownOpen }"
                                    class="h-3 w-3 transition-transform duration-200"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Accounts Payable Submenu -->
                            <div 
                                v-show="accountsPayableDropdownOpen"
                                class="mt-1 ml-4 space-y-1"
                            >
                                <Link 
                                    :href="route('vouchers.index')"
                                    class="block rounded-lg px-4 py-2 text-xs text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-800 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                                >
                                    Vouchers
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reports
                </a>

                <!-- Calendar -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Calendar
                </a>

                <!-- Settings -->
                <a 
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
            </nav>

            <!-- Dark Mode Toggle -->
            <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                <button 
                    @click="toggleDarkMode"
                    class="flex w-full items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg v-if="!darkMode" class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg v-else class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ darkMode ? 'Light Mode' : 'Dark Mode' }}
                </button>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div 
            v-if="sidebarOpen" 
            @click="toggleSidebar"
            class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden"
        ></div>

            <!-- Main Content Area -->
            <div class="flex-1 min-h-screen lg:flex lg:flex-col lg:ml-[290px]">
                <!-- Header adjacent to sidebar -->
                <header class="sticky top-0 z-30 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-600 h-16">
                <div class="flex items-center justify-between px-6 h-16">
                    <!-- Mobile menu button -->
                    <button 
                        @click="toggleSidebar"
                        class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Logo and Title -->
                    <div class="flex items-center min-w-0">
                        <img src="/assets/images/ays_translucent.png" alt="AYS Logo" class="h-8 w-auto mr-3 flex-shrink-0">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white truncate">
                            CRM Dispatch Dashboard
                        </h1>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md mx-4 hidden md:block">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                placeholder="Search or type command..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder-gray-400"
                            >
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative user-dropdown">
                        <div @click="dropdownOpen = !dropdownOpen" class="flex cursor-pointer">
                            <a 
                                href="#" 
                                @click.prevent 
                                class="flex items-center text-gray-700 dark:text-gray-400"
                            >
                                <span class="mr-3 h-9 w-9 overflow-hidden rounded-full">
                                    <img 
                                        src="/assets/images/ays_translucent.png" 
                                        alt="User Avatar" 
                                        class="h-full w-full object-cover bg-gray-100"
                                    >
                                </span>
                                <span class="text-sm mr-1 block font-medium text-gray-800 dark:text-white"> 
                                    {{ $page.props.auth.user.name }}
                                </span>
                            </a>
                        </div>

                        <!-- Dropdown Menu -->
                        <div 
                            v-show="dropdownOpen"
                            class="absolute right-0 mt-4 flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-60"
                        >
                            <!-- User Info -->
                            <div class="flex items-center gap-3 border-b border-gray-200 pb-3 dark:border-gray-700">
                                <div class="h-12 w-12 overflow-hidden rounded-full">
                                    <img 
                                        src="/assets/images/ays_translucent.png" 
                                        alt="User Avatar" 
                                        class="h-full w-full object-cover bg-gray-100"
                                    >
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        {{ $page.props.auth.user.name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $page.props.auth.user.email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-3 space-y-1">
                                <Link 
                                    :href="route('profile.edit')"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    My Profile
                                </Link>

                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                                <Link 
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    Sign Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
                <slot />
            </main>
            </div>
        </div>
    </div>
</template>
