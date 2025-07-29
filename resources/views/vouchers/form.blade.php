@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">
        {{ $voucher->exists ? 'Edit Voucher' : 'Create Voucher' }}
    </h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ $voucher->exists ? route('vouchers.update', $voucher->VoucherId) : route('vouchers.store') }}">
        @csrf
        @if ($voucher->exists)
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Purchase Order ID</label>
            <input type="number" name="PurchaseOrderId" value="{{ old('PurchaseOrderId', $voucher->PurchaseOrderId) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Vendor</label>
            <input type="text" name="Vendor" value="{{ old('Vendor', $voucher->Vendor) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Packing Slip</label>
            <input type="text" name="PackingSlip" value="{{ old('PackingSlip', $voucher->PackingSlip) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Invoice Date</label>
            <input type="date" name="InvoiceDate" value="{{ old('InvoiceDate', $voucher->InvoiceDate ? \Carbon\Carbon::parse($voucher->InvoiceDate)->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Invoice No</label>
            <input type="text" name="InvoiceNo" value="{{ old('InvoiceNo', $voucher->InvoiceNo) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Invoice</label>
            <input type="text" name="Invoice" value="{{ old('Invoice', $voucher->Invoice) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="flex justify-between">
            <a href="{{ route('vouchers.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Back to List
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ $voucher->exists ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</div>
@endsection
