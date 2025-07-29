@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">
        {{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}
    </h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer->ID) : route('customers.store') }}">
        @csrf
        @if ($customer->exists)
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="FirstName" value="{{ old('FirstName', $customer->FirstName) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="LastName" value="{{ old('LastName', $customer->LastName) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="Email" value="{{ old('Email', $customer->Email) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $customer->exists ? 'Update' : 'Create' }}
        </button>
    </form>
</div>
@endsection