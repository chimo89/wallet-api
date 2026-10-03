<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Contoh Project E-Wallet &middot; {{ config('app.name', 'Laravel') }}</title>
    <meta name="description"
          content="Contoh project E-Wallet dengan sistem Top Up, Transfer, dan Payment. Dibangun dengan Laravel dan autentikasi JWT.">

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

        /* Cahaya lembut di belakang hero agar terlihat lebih profesional. */
        .hero-glow {
            background-image:
                radial-gradient(65% 60% at 50% 0%, rgba(99, 102, 241, 0.35) 0%, rgba(2, 6, 23, 0) 70%),
                radial-gradient(45% 45% at 88% 15%, rgba(14, 165, 233, 0.28) 0%, rgba(2, 6, 23, 0) 70%),
                radial-gradient(45% 45% at 10% 25%, rgba(168, 85, 247, 0.22) 0%, rgba(2, 6, 23, 0) 70%);
        }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased">

    {{-- ==================== NAVBAR ==================== --}}
    <header class="absolute inset-x-0 top-0 z-20">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-5">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold text-white">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-lg ring-1 ring-inset ring-white/20">💳</span>
                <span>Wallet<span class="text-white/60">API</span></span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ url('/register') }}"
                   class="hidden rounded-xl px-4 py-2 text-sm font-semibold text-white/80 transition hover:text-white sm:inline-block">
                    Daftar
                </a>
                <a href="{{ url('/login') }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                    Login
                </a>
            </div>
        </nav>
    </header>

    {{-- ==================== HERO ==================== --}}
    <section class="hero-glow relative overflow-hidden bg-slate-950 pb-20 pt-28 sm:pt-32">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold text-indigo-200 ring-1 ring-inset ring-white/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Contoh Project &middot; Laravel + JWT Auth
                </span>

                <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">
                    Contoh Project
                    <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-sky-400 bg-clip-text text-transparent">E-Wallet</span>
                </h1>

                <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                    Ini adalah contoh project E-Wallet dengan sistem
                    <strong class="font-semibold text-white">Top Up</strong>,
                    <strong class="font-semibold text-white">Transfer</strong>, dan
                    <strong class="font-semibold text-white">Payment</strong>
                    &mdash; dilengkapi riwayat transaksi, autentikasi token JWT, dan keamanan PIN 6 digit.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/register') }}"
                       class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:bg-indigo-500">
                        Daftar Sekarang
                    </a>
                    <a href="{{ url('/login') }}"
                       class="rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-inset ring-white/20 transition hover:bg-white/15">
                        Lihat Dashboard
                    </a>
                </div>

                <p class="mt-4 text-xs text-slate-400">Masuk menggunakan nomor HP dan PIN 6 digit.</p>
            </div>

            {{-- Gambar pratinjau dashboard --}}
            <div class="relative mx-auto mt-14 max-w-5xl">
                <div class="absolute -inset-6 rounded-[2rem] bg-gradient-to-r from-indigo-500/30 via-violet-500/25 to-sky-500/30 blur-2xl"></div>

                <figure class="relative overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-white/10">
                    <img src="{{ asset('images/dashboard-preview.svg') }}"
                         alt="Pratinjau dashboard E-Wallet: kartu saldo, tombol Top Up, Bayar, Transfer, dan riwayat transaksi"
                         class="block w-full" width="1280" height="800" loading="eager">
                </figure>

                <p class="mt-4 text-center text-xs text-slate-400">
                    Pratinjau halaman transaksi &mdash; <span class="font-mono text-slate-300">/wallet</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ==================== FITUR ==================== --}}
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Fitur</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Tiga transaksi inti e-wallet</h2>
                <p class="mt-4 text-slate-500">
                    Setiap fitur sudah terhubung ke endpoint API dan dapat langsung dipakai dari halaman dashboard.
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    [
                        'icon' => '⬆️',
                        'title' => 'Top Up',
                        'desc' => 'Menambah saldo ke akun. Nominal minimal Rp 1.000 dan saldo langsung diperbarui.',
                        'endpoint' => 'POST /api/topup',
                    ],
                    [
                        'icon' => '🔁',
                        'title' => 'Transfer',
                        'desc' => 'Mengirim saldo ke pengguna lain hanya dengan memasukkan nomor HP tujuan.',
                        'endpoint' => 'POST /api/transfer',
                    ],
                    [
                        'icon' => '🧾',
                        'title' => 'Payment',
                        'desc' => 'Membayar tagihan disertai keterangan. Saldo otomatis berkurang setelah berhasil.',
                        'endpoint' => 'POST /api/pay',
                    ],
                    [
                        'icon' => '📜',
                        'title' => 'Riwayat Transaksi',
                        'desc' => 'Semua transaksi tercatat lengkap: tipe debit/kredit, nominal, serta saldo sebelum dan sesudah.',
                        'endpoint' => 'GET /api/transactions',
                    ],
                    [
                        'icon' => '🔐',
                        'title' => 'Autentikasi JWT',
                        'desc' => 'Login menghasilkan access token dan refresh token. PIN 6 digit disimpan dalam bentuk hash.',
                        'endpoint' => 'POST /api/login',
                    ],
                    [
                        'icon' => '👤',
                        'title' => 'Profil &amp; Saldo',
                        'desc' => 'Menampilkan nama pengguna dan saldo terkini langsung dari data akun.',
                        'endpoint' => 'GET /api/profile',
                    ],
                ] as $feature)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-xl">{{ $feature['icon'] }}</span>
                        <h3 class="mt-4 text-base font-semibold text-slate-900">{!! $feature['title'] !!}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $feature['desc'] }}</p>
                        <code class="mt-4 inline-block rounded-lg bg-slate-900 px-3 py-1.5 font-mono text-[11px] text-slate-300">{{ $feature['endpoint'] }}</code>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== CARA KERJA ==================== --}}
    <section class="bg-white py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Cara Kerja</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Mulai dalam tiga langkah</h2>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ([
                    [
                        'step' => '01',
                        'title' => 'Daftar Akun',
                        'desc' => 'Isi nama, nomor HP, alamat, lalu buat PIN 6 digit. Nomor HP harus unik.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Login',
                        'desc' => 'Masuk dengan nomor HP dan PIN. Sistem mengembalikan token JWT untuk mengakses API.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Bertransaksi',
                        'desc' => 'Top up, bayar, atau transfer saldo. Semua tercatat otomatis di riwayat transaksi.',
                    ],
                ] as $step)
                    <div class="rounded-2xl border border-slate-200 p-6">
                        <span class="wallet-gradient inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold text-white">{{ $step['step'] }}</span>
                        <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 flex flex-wrap items-center justify-center gap-2">
                @foreach (['Laravel ' . app()->version(), 'JWT Auth', 'SQLite', 'Tailwind CSS', 'PHP 8.4'] as $tech)
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs font-semibold text-slate-600">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== CTA ==================== --}}
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="wallet-gradient overflow-hidden rounded-3xl px-8 py-14 text-center shadow-xl">
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Siap mencoba?</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/85">
                    Buat akun contoh, lalu lakukan top up dan transfer antar pengguna untuk melihat seluruh alur berjalan.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/register') }}"
                       class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                        Buat Akun
                    </a>
                    <a href="{{ url('/login') }}"
                       class="rounded-xl bg-white/15 px-6 py-3 text-sm font-semibold text-white ring-1 ring-inset ring-white/25 transition hover:bg-white/25">
                        Masuk ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="border-t border-slate-200 bg-white py-8">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 text-xs text-slate-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} &mdash; contoh project E-Wallet.</p>
            <p>Dibangun dengan Laravel {{ app()->version() }}.</p>
        </div>
    </footer>
</body>
</html>