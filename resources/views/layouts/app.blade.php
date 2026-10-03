<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'E-Wallet') &middot; {{ config('app.name', 'Laravel') }}</title>

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
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col">
        <header class="wallet-gradient text-white shadow-lg">
            <div class="mx-auto flex w-full max-w-5xl items-center justify-between gap-4 px-4 py-4">
                <a href="{{ url('/wallet') }}" class="flex items-center gap-2 font-semibold tracking-wide">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-lg">💳</span>
                    <span>Wallet<span class="text-white/70">API</span></span>
                </a>

                @hasSection('header-actions')
                    <div class="flex items-center gap-3">@yield('header-actions')</div>
                @endif
            </div>
        </header>

        <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-8">
            @yield('content')
        </main>

        <footer class="py-6 text-center text-xs text-slate-400">
            {{ config('app.name', 'Laravel') }} &mdash; Laravel {{ app()->version() }}
        </footer>
    </div>

    @stack('scripts')
</body>
</html>