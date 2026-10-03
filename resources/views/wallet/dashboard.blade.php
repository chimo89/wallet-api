@extends('layouts.app')

@section('title', 'Transaksi Wallet')

@section('header-actions')
    <span class="text-xs text-white/80">
        Halo, <span id="user-name" class="font-semibold text-white">...</span>
    </span>
    <button type="button" id="logout-btn"
            class="rounded-lg bg-white/15 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/25">
        Logout
    </button>
@endsection

@section('content')
    <div id="alert" class="hidden"></div>

    {{-- Kartu saldo: nilainya diambil dari kolom `balance` tabel users via GET /api/profile --}}
    <section class="wallet-gradient relative overflow-hidden rounded-2xl p-6 text-white shadow-xl">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-white/75">Saldo Saat Ini</p>
                <p id="balance" class="mt-1 text-3xl font-bold tracking-tight">Rp 0</p>
                <p class="mt-2 text-xs text-white/60">Saldo terkini dari profil akun Anda.</p>
            </div>
            <button type="button" id="refresh-btn"
                    class="rounded-xl bg-white/15 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/25">
                &#8635; Perbarui
            </button>
        </div>
    </section>

    {{-- Form transaksi sesuai endpoint WalletController --}}
    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <button type="button" data-tab="topup"
                    class="tab-btn rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition">
                Top Up
            </button>
            <button type="button" data-tab="pay"
                    class="tab-btn rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                Bayar
            </button>
            <button type="button" data-tab="transfer"
                    class="tab-btn rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                Transfer
            </button>
        </div>

        <div class="mt-6">
            {{-- POST /api/topup --}}
            <div id="panel-topup" class="tab-panel">
                <form id="topup-form" class="space-y-4" novalidate>
                    <div>
                        <label for="topup-amount" class="block text-sm font-medium text-slate-700">Nominal Top Up</label>
                        <input type="number" id="topup-amount" name="amount" min="1000" step="1000" placeholder="50000"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <p class="mt-1 text-xs text-slate-400">Minimal Rp 1.000.</p>
                    </div>
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Top Up
                    </button>
                </form>
            </div>

            {{-- POST /api/pay --}}
            <div id="panel-pay" class="tab-panel hidden">
                <form id="pay-form" class="space-y-4" novalidate>
                    <div>
                        <label for="pay-amount" class="block text-sm font-medium text-slate-700">Nominal Pembayaran</label>
                        <input type="number" id="pay-amount" name="amount" min="1" step="1" placeholder="25000"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label for="pay-remarks" class="block text-sm font-medium text-slate-700">Keterangan</label>
                        <input type="text" id="pay-remarks" name="remarks" placeholder="Bayar listrik"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Bayar
                    </button>
                </form>
            </div>

            {{-- POST /api/transfer --}}
            <div id="panel-transfer" class="tab-panel hidden">
                <form id="transfer-form" class="space-y-4" novalidate>
                    <div>
                        <label for="transfer-target" class="block text-sm font-medium text-slate-700">Nomor HP Penerima</label>
                        <input type="tel" id="transfer-target" name="target_user" inputmode="numeric" placeholder="08123456789"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <p class="mt-1 text-xs text-slate-400">
                            Nomor HP Anda: <span id="own-phone" class="font-semibold text-slate-500">-</span>
                            &mdash; bagikan nomor ini agar orang lain bisa transfer ke Anda.
                        </p>
                    </div>
                    <div>
                        <label for="transfer-amount" class="block text-sm font-medium text-slate-700">Nominal Transfer</label>
                        <input type="number" id="transfer-amount" name="amount" min="1" step="1" placeholder="10000"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label for="transfer-remarks" class="block text-sm font-medium text-slate-700">Keterangan</label>
                        <input type="text" id="transfer-remarks" name="remarks" placeholder="Bayar utang makan"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Transfer
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- GET /api/transactions --}}
    <section class="mt-6 rounded-2xl bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-slate-800">Riwayat Transaksi</h2>
            <span class="text-xs text-slate-400">Terbaru di atas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wide text-slate-400">
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Transaksi</th>
                        <th class="px-4 py-3 font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-right font-semibold">Nominal</th>
                        <th class="px-4 py-3 text-right font-semibold">Saldo Akhir</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody id="history-body">
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Memuat transaksi...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    @include('partials.wallet-api')
    <script>
        (function () {
            var TAB_ACTIVE = 'tab-btn rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition';
            var TAB_IDLE = 'tab-btn rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200';

            function activateTab(name) {
                document.querySelectorAll('.tab-btn').forEach(function (button) {
                    button.className = button.dataset.tab === name ? TAB_ACTIVE : TAB_IDLE;
                });

                document.querySelectorAll('.tab-panel').forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.id !== 'panel-' + name);
                });
            }

            // Menentukan label & ID transaksi dari nama field dinamis pada response API.
            function describe(transaction) {
                if (transaction.top_up_id) {
                    return { label: 'Top Up', id: transaction.top_up_id };
                }
                if (transaction.payment_id) {
                    return { label: 'Pembayaran', id: transaction.payment_id };
                }
                if (transaction.transfer_id) {
                    return { label: 'Transfer', id: transaction.transfer_id };
                }

                return { label: 'Transaksi', id: transaction.id || '-' };
            }

            // Saldo diambil langsung dari kolom `balance` tabel users melalui GET /api/profile.
            function renderBalance(value) {
                document.getElementById('balance').textContent = WalletUI.rupiah(value);
            }

            // Memuat profil user yang sedang login: nama, nomor HP, dan saldo.
            function loadProfile() {
                return WalletApi.request('/profile', { method: 'GET', auth: true })
                    .then(function (response) {
                        if (!response.ok || response.data.status !== 'SUCCESS') {
                            throw new Error(response.data.message || 'Gagal memuat profil (HTTP ' + response.status + ').');
                        }

                        var profile = response.data.result;
                        var fullName = (profile.first_name + ' ' + profile.last_name).trim();

                        document.getElementById('user-name').textContent = fullName || profile.phone_number;
                        document.getElementById('own-phone').textContent = profile.phone_number;

                        renderBalance(profile.balance);
                    })
                    .catch(function (error) {
                        WalletUI.alert('alert', 'error', error.message || 'Gagal memuat profil.');
                    });
            }

            function renderHistory(list) {
                var tbody = document.getElementById('history-body');

                if (!list.length) {
                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Belum ada transaksi.</td></tr>';

                    return;
                }

                tbody.innerHTML = list.map(function (transaction) {
                    var info = describe(transaction);
                    var isCredit = transaction.transaction_type === 'CREDIT';
                    var badge = isCredit
                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                        : 'bg-rose-50 text-rose-700 ring-rose-200';

                    return '<tr class="border-b border-slate-100 align-top last:border-0">'
                        + '<td class="whitespace-nowrap px-4 py-3 text-xs text-slate-500">' + WalletUI.escape(transaction.created_date) + '</td>'
                        + '<td class="px-4 py-3">'
                        + '<div class="text-sm font-semibold text-slate-800">' + WalletUI.escape(info.label) + '</div>'
                        + '<div class="text-xs text-slate-400">' + WalletUI.escape(transaction.remarks || '-') + '</div>'
                        + '<div class="mt-1 break-all font-mono text-[10px] text-slate-400">' + WalletUI.escape(info.id) + '</div>'
                        + '</td>'
                        + '<td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ' + badge + '">'
                        + WalletUI.escape(transaction.transaction_type) + '</span></td>'
                        + '<td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold ' + (isCredit ? 'text-emerald-600' : 'text-red-600') + '">'
                        + (isCredit ? '+ ' : '- ') + WalletUI.rupiah(transaction.amount) + '</td>'
                        + '<td class="whitespace-nowrap px-4 py-3 text-right text-xs text-slate-500">' + WalletUI.rupiah(transaction.balance_after) + '</td>'
                        + '<td class="px-4 py-3 text-center text-[10px] font-semibold text-slate-500">' + WalletUI.escape(transaction.status) + '</td>'
                        + '</tr>';
                }).join('');
            }

            // Handler generik: kirim form ke endpoint API, tampilkan hasil, lalu refresh riwayat.
            function submitForm(config) {
                var form = document.getElementById(config.formId);
                var button = form.querySelector('button[type="submit"]');
                var label = button.textContent.trim();

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    WalletUI.clear('alert');

                    var payload = config.build(form);
                    var error = config.validate ? config.validate(payload) : null;

                    if (error) {
                        WalletUI.alert('alert', 'error', error);

                        return;
                    }

                    button.disabled = true;
                    button.textContent = 'Memproses...';

                    WalletApi.request(config.path, { body: payload, auth: true })
                        .then(function (response) {
                            if (response.ok && response.data.status === 'SUCCESS') {
                                WalletUI.alert('alert', 'success', config.successMessage(response.data.result));
                                form.reset();

                                // Saldo & nama ikut berubah setelah transaksi berhasil.
                                loadProfile();

                                return loadTransactions();
                            }

                            WalletUI.alert(
                                'alert',
                                'error',
                                response.data.message || 'Transaksi gagal (HTTP ' + response.status + ').'
                            );
                        })
                        .catch(function (requestError) {
                            WalletUI.alert('alert', 'error', requestError.message || 'Terjadi kesalahan pada server.');
                        })
                        .finally(function () {
                            button.disabled = false;
                            button.textContent = label;
                        });
                });
            }

            function loadTransactions() {
                return WalletApi.request('/transactions', { method: 'GET', auth: true })
                    .then(function (response) {
                        if (response.ok && response.data.status === 'SUCCESS') {
                            renderHistory(response.data.result || []);
                        } else {
                            WalletUI.alert(
                                'alert',
                                'error',
                                response.data.message || 'Gagal memuat riwayat transaksi (HTTP ' + response.status + ').'
                            );
                            renderHistory([]);
                        }
                    })
                    .catch(function (error) {
                        WalletUI.alert('alert', 'error', error.message || 'Gagal memuat riwayat transaksi.');
                        renderHistory([]);
                    });
            }
            document.addEventListener('DOMContentLoaded', function () {
                if (!WalletApi.requireAuth()) {
                    return;
                }

                document.querySelectorAll('.tab-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        activateTab(button.dataset.tab);
                    });
                });

                document.getElementById('logout-btn').addEventListener('click', function () {
                    WalletApi.clearTokens();
                    window.location.href = WalletApi.loginUrl;
                });

                document.getElementById('refresh-btn').addEventListener('click', function () {
                    WalletUI.clear('alert');
                    loadProfile();
                    loadTransactions();
                });

                // POST /api/topup -> amount (min 1000)
                submitForm({
                    formId: 'topup-form',
                    path: '/topup',
                    build: function (form) {
                        return { amount: Number(form.amount.value) };
                    },
                    validate: function (payload) {
                        return payload.amount >= 1000 ? null : 'Minimal top up adalah Rp 1.000.';
                    },
                    successMessage: function (result) {
                        return 'Top up berhasil. Saldo: ' + WalletUI.rupiah(result.balance_before)
                            + ' menjadi ' + WalletUI.rupiah(result.balance_after) + '.';
                    },
                });

                // POST /api/pay -> amount (min 1) & remarks
                submitForm({
                    formId: 'pay-form',
                    path: '/pay',
                    build: function (form) {
                        return { amount: Number(form.amount.value), remarks: form.remarks.value.trim() };
                    },
                    validate: function (payload) {
                        if (!payload.amount || payload.amount < 1) {
                            return 'Nominal pembayaran minimal Rp 1.';
                        }

                        return payload.remarks ? null : 'Keterangan (remarks) wajib diisi.';
                    },
                    successMessage: function (result) {
                        return 'Pembayaran "' + result.remarks + '" berhasil. Saldo akhir: ' + WalletUI.rupiah(result.balance_after) + '.';
                    },
                });

                // POST /api/transfer -> target_user = NOMOR HP penerima (bukan UUID)
                submitForm({
                    formId: 'transfer-form',
                    path: '/transfer',
                    build: function (form) {
                        return {
                            target_user: form.target_user.value.trim(),
                            amount: Number(form.amount.value),
                            remarks: form.remarks.value.trim(),
                        };
                    },
                    validate: function (payload) {
                        var phonePattern = /^[0-9]{8,20}$/;

                        if (!phonePattern.test(payload.target_user)) {
                            return 'Nomor HP penerima harus berupa angka (8-20 digit).';
                        }

                        if (!payload.amount || payload.amount < 1) {
                            return 'Nominal transfer minimal Rp 1.';
                        }

                        return payload.remarks ? null : 'Keterangan (remarks) wajib diisi.';
                    },
                    successMessage: function (result) {
                        return 'Transfer "' + result.remarks + '" berhasil. Saldo akhir: ' + WalletUI.rupiah(result.balance_after) + '.';
                    },
                });

                activateTab('topup');
                loadProfile();
                loadTransactions();
            });
        })();
    </script>
@endpush