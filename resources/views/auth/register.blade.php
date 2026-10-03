@extends('layouts.guest')

@section('title', 'Daftar Akun')
@section('subtitle', 'Buat akun e-wallet baru')

@section('content')
    <h2 class="text-lg font-semibold text-slate-900">Register</h2>
    <p class="mt-1 text-sm text-slate-500">Lengkapi data di bawah ini untuk membuat akun.</p>

    <div id="alert" class="hidden"></div>

    {{-- Field mengikuti validasi AuthController@register --}}
    <form id="register-form" class="mt-6 space-y-4" novalidate>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700">Nama Depan</label>
                <input type="text" id="first_name" name="first_name" autocomplete="given-name" placeholder="Budi"
                       class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700">Nama Belakang</label>
                <input type="text" id="last_name" name="last_name" autocomplete="family-name" placeholder="Santoso"
                       class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>
        </div>

        <div>
            <label for="phone_number" class="block text-sm font-medium text-slate-700">Nomor HP</label>
            <input type="tel" id="phone_number" name="phone_number" autocomplete="tel" placeholder="08123456789"
                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            <p class="mt-1 text-xs text-slate-400">Nomor HP harus unik &mdash; belum pernah terdaftar sebelumnya.</p>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
            <textarea id="address" name="address" rows="2" autocomplete="street-address" placeholder="Jl. Merdeka No. 10, Jakarta"
                      class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"></textarea>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="pin" class="block text-sm font-medium text-slate-700">PIN (6 digit)</label>
                <input type="password" id="pin" name="pin" inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
                       placeholder="••••••" autocomplete="new-password"
                       class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm tracking-[0.4em] text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <label for="pin_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi PIN</label>
                <input type="password" id="pin_confirmation" name="pin_confirmation" inputmode="numeric" maxlength="6"
                       placeholder="••••••" autocomplete="new-password"
                       class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm tracking-[0.4em] text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>
        </div>

        <button type="submit" id="submit-btn"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:cursor-not-allowed disabled:opacity-60">
            Daftar Sekarang
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ url('/login') }}" class="font-semibold text-indigo-600 hover:underline">Login di sini</a>
    </p>
@endsection

@push('scripts')
    @include('partials.wallet-api')
    <script>
        (function () {
            var form = document.getElementById('register-form');
            var button = document.getElementById('submit-btn');
            var successRedirect = @json(url('/login'));

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                WalletUI.clear('alert');

                var payload = {
                    first_name: form.first_name.value.trim(),
                    last_name: form.last_name.value.trim(),
                    phone_number: form.phone_number.value.trim(),
                    address: form.address.value.trim(),
                    pin: form.pin.value.trim(),
                };

                if (!payload.first_name || !payload.last_name || !payload.phone_number || !payload.address || !payload.pin) {
                    WalletUI.alert('alert', 'error', 'Semua kolom wajib diisi.');

                    return;
                }

                if (!/^[0-9]{6}$/.test(payload.pin)) {
                    WalletUI.alert('alert', 'error', 'PIN harus terdiri dari 6 digit angka.');

                    return;
                }

                if (payload.pin !== form.pin_confirmation.value.trim()) {
                    WalletUI.alert('alert', 'error', 'Konfirmasi PIN tidak sama dengan PIN.');

                    return;
                }

                button.disabled = true;
                button.textContent = 'Memproses...';

                WalletApi.request('/register', { body: payload })
                    .then(function (response) {
                        if (response.ok && response.data.status === 'SUCCESS') {
                            var user = response.data.result;

                            WalletUI.alert(
                                'alert',
                                'success',
                                'Registrasi berhasil! Nomor HP Anda: ' + user.phone_number + '. Mengalihkan ke halaman login...'
                            );

                            form.reset();

                            window.setTimeout(function () {
                                window.location.href = successRedirect;
                            }, 2000);
                        } else {
                            WalletUI.alert('alert', 'error', response.data.message || 'Registrasi gagal.');
                        }
                    })
                    .catch(function (error) {
                        WalletUI.alert('alert', 'error', error.message || 'Terjadi kesalahan pada server.');
                    })
                    .finally(function () {
                        button.disabled = false;
                        button.textContent = 'Daftar Sekarang';
                    });
            });
        })();
    </script>
@endpush