@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center">
    <div class="bg-white p-10 rounded-3xl shadow-xl max-w-lg w-full">
        <div class="text-6xl mb-4">🔐</div>
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">401</h1>
        <p class="text-xl text-gray-600 mb-8">Nieautoryzowany dostęp. Musisz się zalogować, aby zobaczyć tę stronę.</p>
        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent text-base font-medium rounded-xl shadow-sm text-white hover:bg-indigo-700 transition">
            Przejdź do logowania
        </a>
    </div>
</div>
@endsection
