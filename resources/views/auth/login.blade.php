@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Zaloguj się</h2>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                Zaloguj
            </button>
        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-indigo-700 text-sm">
            <p class="font-bold mb-1">Dostęp testowy:</p>
            <p>Email: <span class="font-mono">user@example.com</span></p>
            <p>Hasło: <span class="font-mono">user1234</span></p>
        </div>
    </div>
</div>
@endsection
