<template>
    <Head title="Vouchers - Accounts Payable" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                    Accounts Payable - Vouchers
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Loading state -->
                    <div v-if="loading" class="p-6">
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Error state -->
                    <div v-else-if="errorMessage" class="p-6 text-center">
                        <div class="text-red-600 dark:text-red-400">
                            <h3 class="text-lg font-medium mb-2">Error Loading Vouchers</h3>
                            <p class="text-sm">{{ errorMessage }}</p>
                            <button 
                                @click="refreshPage"
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Try Again
                            </button>
                        </div>
                    </div>

                    <!-- Search and Content -->
                    <div v-else class="p-6">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <input 
                                        v-model="search"
                                        type="text" 
                                        placeholder="Search vouchers..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                <div>
                                    <select 
                                        v-model="perPage"
                                        @change="updatePerPage"
                                        class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="10">10 per page</option>
                                        <option value="25">25 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Vouchers Table with proper overflow handling -->
                        <div class="overflow-x-auto overflow-y-visible shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            PO Reference
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Vendor
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Packing Slip
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Invoice Date
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Invoice No
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Invoice
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Posted
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="paginatedVouchers.length === 0">
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <p class="text-lg font-medium">No vouchers found</p>
                                                <p class="text-sm">{{ search ? 'Try adjusting your search criteria' : 'No vouchers available' }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else v-for="voucher in paginatedVouchers" :key="voucher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap max-w-32 truncate" :title="voucher.purchase_order?.ref_number || voucher.po_ref_number || 'N/A'">
                                            {{ voucher.purchase_order?.ref_number || voucher.po_ref_number || 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap max-w-40 truncate" :title="voucher.vendor?.name || voucher.vendor_name || 'N/A'">
                                            {{ voucher.vendor?.name || voucher.vendor_name || 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap max-w-32 truncate" :title="voucher.packing_slip || 'N/A'">
                                            {{ getFileName(voucher.packing_slip) || 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ voucher.invoice_date ? formatDate(voucher.invoice_date) : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap max-w-32 truncate" :title="voucher.invoice_no || 'N/A'">
                                            {{ voucher.invoice_no || 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap max-w-48 truncate" :title="voucher.invoice || 'N/A'">
                                            {{ getFileName(voucher.invoice) || 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span v-if="voucher.invoice_posted" 
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Posted
                                            </span>
                                            <span v-else 
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Pending
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="filteredVouchers.length > 0" class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ startIndex + 1 }} to {{ Math.min(startIndex + perPage, filteredVouchers.length) }} of {{ filteredVouchers.length }} results
                            </div>
                            <div class="flex space-x-2">
                                <button 
                                    @click="previousPage"
                                    :disabled="currentPage === 1"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded disabled:opacity-50 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700"
                                >
                                    Previous
                                </button>
                                <span class="px-3 py-1 text-sm">
                                    Page {{ currentPage }} of {{ totalPages }}
                                </span>
                                <button 
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded disabled:opacity-50 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// Props
const props = defineProps({
    vouchers: {
        type: Array,
        default: () => []
    },
    error: {
        type: String,
        default: null
    }
});

// Reactive data
const vouchers = ref(props.vouchers || []);
const loading = ref(false);
const search = ref('');
const currentPage = ref(1);
const perPage = ref(25);
const errorMessage = ref(props.error);

// Computed properties
const filteredVouchers = computed(() => {
    if (!search.value) return vouchers.value;
    
    const searchTerm = search.value.toLowerCase();
    return vouchers.value.filter(voucher => {
        return (
            (voucher.purchase_order?.ref_number || voucher.po_ref_number || '').toLowerCase().includes(searchTerm) ||
            (voucher.vendor?.name || voucher.vendor_name || '').toLowerCase().includes(searchTerm) ||
            (voucher.packing_slip || '').toLowerCase().includes(searchTerm) ||
            (voucher.invoice_no || '').toLowerCase().includes(searchTerm) ||
            (voucher.invoice || '').toLowerCase().includes(searchTerm)
        );
    });
});

const totalPages = computed(() => {
    return Math.ceil(filteredVouchers.value.length / perPage.value);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * perPage.value;
});

const paginatedVouchers = computed(() => {
    const start = startIndex.value;
    const end = start + perPage.value;
    return filteredVouchers.value.slice(start, end);
});

// Methods
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return 'Invalid Date';
    }
};

const getFileName = (filePath) => {
    if (!filePath) return null;
    // Extract filename from path like "F:\AP\PS\ps32190.pdf" or "F:\AP\Invoice\14c71b61-2b70-4867-a5ee-e916f8d15e46.pdf"
    const parts = filePath.split(/[\\\/]/);
    return parts[parts.length - 1];
};

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const updatePerPage = () => {
    currentPage.value = 1;
};

const refreshPage = () => {
    window.location.reload();
};

// Watch for search changes
watch(() => search.value, () => {
    currentPage.value = 1;
});
</script>
