@extends('layouts.guest')

@section('title', 'Login')
@section('subtitle', 'Masuk ke akun e-wallet Anda')

@section('content')
    <h2 class="text-lg font-semibold text-slate-900">Login</h2>
    <p class="mt-1 text-sm text-slate-500">Gunakan nomor HP dan PIN yang sudah terdaftar.</p>

    <div id="alert" class="hidden"></div>

    {{-- Field mengikuti AuthController@login --}}
    <form id="login-form" class="mt-6 space-y-4" novalidate>
        <div>
            <label for="phone_number" class="block text-sm font-medium text-slate-700">Nomor HP</label>
            <input type="tel" id="phone_number" name="phone_number" autocomplete="tel" placeholder="08123456789"
                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
        </div>

        <div>
            <label for="pin" class="block text-sm font-medium text-slate-700">PIN (6 digit)</label>
            <input type="password" id="pin" name="pin" inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
                   placeholder="••••••" autocomplete="current-password"
                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm tracking-[0.4em] text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
        </div>

        <button type="submit" id="submit-btn"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:cursor-not-allowed disabled:opacity-60">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ url('/register') }}" class="font-semibold text-indigo-600 hover:underline">Daftar di sini</a>
    </p>
@endsection

@push('scripts')
    @include('partials.wallet-api')
    <script>
        (function () {
            var form = document.getElementById('login-form');
            var button = document.getElementById('submit-btn');
            var dashboardUrl = @json(url('/wallet'));

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                WalletUI.clear('alert');

                var payload = {
                    phone_number: form.phone_number.value.trim(),
                    pin: form.pin.value.trim(),
                };

                if (!payload.phone_number || !payload.pin) {
                    WalletUI.alert('alert', 'error', 'Nomor HP dan PIN wajib diisi.');

                    return;
                }

                button.disabled = true;
                button.textContent = 'Memproses...';

                WalletApi.request('/login', { body: payload })
                    .then(function (response) {
                        if (response.ok && response.data.status === 'SUCCESS') {
                            var result = response.data.result;

                            // Simpan access & refresh token lalu masuk ke halaman transaksi.
                            WalletApi.setTokens(result.access_token, result.refresh_token);

                            window.location.href = dashboardUrl;
                        } else {
                            WalletUI.alert('alert', 'error', response.data.message || 'Login gagal.');
                        }
                    })
                    .catch(function (error) {
                        WalletUI.alert('alert', 'error', error.message || 'Terjadi kesalahan pada server.');
                    })
                    .finally(function () {
                        button.disabled = false;
                        button.textContent = 'Masuk';
                    });
            });
        })();
    </script>
@endpush