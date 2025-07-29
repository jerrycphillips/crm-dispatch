@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Voucher Details</h2>
            <div class="flex space-x-2">
                <a href="{{ route('vouchers.edit', $voucher->VoucherId) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Edit Voucher
                </a>
                <a href="{{ route('vouchers.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Voucher Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Voucher Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Voucher ID</label>
                    <p class="text-gray-900">{{ $voucher->VoucherId }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Purchase Order Ref Number</label>
                    <p class="text-gray-900">{{ $voucher->purchaseOrder->RefNumber ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Vendor Name</label>
                    <p class="text-gray-900">{{ $voucher->vendor->Name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Invoice Date</label>
                    <p class="text-gray-900">
                        {{ $voucher->InvoiceDate ? \Carbon\Carbon::parse($voucher->InvoiceDate)->format('m/d/Y') : 'N/A' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Invoice Number</label>
                    <p class="text-gray-900">{{ $voucher->InvoiceNo ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Invoice Posted</label>
                    @if($voucher->InvoicePosted)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Posted
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @endif
                </div>
            </div>

            <!-- Files and Documents -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Packing Slip</label>
                    @if($voucher->packing_slip_url)
                        <a href="{{ $voucher->packing_slip_url }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Packing Slip
                        </a>
                    @else
                        <p class="text-gray-400">No packing slip available</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Invoice</label>
                    @if($voucher->invoice_url)
                        <a href="{{ $voucher->invoice_url }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Invoice
                        </a>
                    @else
                        <p class="text-gray-400">No invoice available</p>
                    @endif
                </div>

                @if($voucher->PSFiledTime)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PS Filed Time</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($voucher->PSFiledTime)->format('m/d/Y g:i A') }}</p>
                    </div>
                @endif

                @if($voucher->InvoiceTotal)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Invoice Total</label>
                        <p class="text-gray-900">${{ number_format($voucher->InvoiceTotal, 2) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Details -->
        @if($voucher->purchaseOrder)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Order Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PO Number</label>
                        <p class="text-gray-900">{{ $voucher->purchaseOrder->PurchaseOrderNo ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PO Date</label>
                        <p class="text-gray-900">
                            {{ $voucher->purchaseOrder->Date ? \Carbon\Carbon::parse($voucher->purchaseOrder->Date)->format('m/d/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Job Number</label>
                        <p class="text-gray-900">{{ $voucher->purchaseOrder->JobNo ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
