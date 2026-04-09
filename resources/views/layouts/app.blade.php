<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMF Young Tech Challenge - Faktury</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-indigo-600">SMF Challenge</span>
                    </div>
                    @auth
                    <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('invoices.index', 'invoices.show', 'invoices.edit') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition">
                            Faktury
                        </a>
                        <a href="{{ route('invoices.ocr') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('invoices.ocr', 'invoices.process-ocr', 'invoices.store_ocr') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition">
                            Analizuj OCR 🤖
                        </a>
                        <a href="{{ route('invoices.generate_form') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('invoices.generate_form') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition">
                            Generator Faktur 📄
                        </a>
                        <a href="/api-docs.html" target="_blank" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 text-sm font-medium transition">
                            API Documentation 🛠️
                        </a>
                    </div>
                    @endauth
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Wyloguj</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-3 py-1 rounded-lg">Zaloguj</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-amber-50 border-l-4 border-amber-400 p-4">
                    <ul class="list-disc list-inside text-sm text-amber-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
