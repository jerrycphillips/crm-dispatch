<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                CRM Dashboard
            </h2>
        </template>

        <!-- Dashboard Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Customers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">162</p>
                        <p class="text-sm text-green-600 dark:text-green-400">+12% from last month</p>
                    </div>
                </div>
            </div>

            <!-- Active Sites -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Sites</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">89</p>
                        <p class="text-sm text-green-600 dark:text-green-400">+3% from last month</p>
                    </div>
                </div>
            </div>

            <!-- Total Employees -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Employees</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">162</p>
                        <p class="text-sm text-blue-600 dark:text-blue-400">4 with login access</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">$24,750</p>
                        <p class="text-sm text-green-600 dark:text-green-400">+8% from last month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-green-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">New customer added: ABC Corp</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-blue-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Site inspection completed: Downtown Office</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">4 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-yellow-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Employee schedule updated</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">6 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-red-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Maintenance request: Facility A</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/50 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/70 transition-colors">
                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-300">Add Customer</span>
                        </button>
                        <button class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/50 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/70 transition-colors">
                            <svg class="h-8 w-8 text-green-600 dark:text-green-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-900 dark:text-green-300">New Site</span>
                        </button>
                        <button class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/50 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/70 transition-colors">
                            <svg class="h-8 w-8 text-purple-600 dark:text-purple-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-purple-900 dark:text-purple-300">View Reports</span>
                        </button>
                        <button class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/70 transition-colors">
                            <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-900 dark:text-yellow-300">Schedule</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
