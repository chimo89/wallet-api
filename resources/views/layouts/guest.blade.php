<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Masuk') &middot; {{ config('app.name', 'Laravel') }}</title>

    {{--
        Memakai asset Vite bila sudah di-build (`npm run build`).
        Karena environment ini belum memiliki Node.js/npm, dipakai Tailwind CDN
        sebagai fallback agar tampilan tetap berjalan tanpa proses build.
    --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        .wallet-gradient {
            background-image: linear-gradient(135deg, #4f46e5 0%, #7c3aed 45%, #0ea5e9 100%);
        }
    </style>
</head>
<body class="wallet-gradient flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-lg font-semibold tracking-wide">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-lg">💳</span>
                <span>Wallet<span class="text-white/70">API</span></span>
            </a>
            <p class="mt-2 text-sm text-white/75">@yield('subtitle')</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>