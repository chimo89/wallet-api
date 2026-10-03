{{--
    Helper front-end untuk berkomunikasi dengan API E-Wallet (lihat routes/api.php).

    - Token JWT (access & refresh) disimpan di localStorage agar halaman tetap stateless.
    - Semua request memakai header `Accept: application/json` dan
      `Authorization: Bearer <access_token>` untuk endpoint yang butuh login.
    - Bila API membalas 401, token dihapus dan user dialihkan ke halaman login.
--}}
<script>
    window.WalletApi = (function () {
        var TOKEN_KEY = 'wallet_access_token';
        var REFRESH_KEY = 'wallet_refresh_token';
        var baseUrl = @json(url('/api'));
        var loginUrl = @json(url('/login'));

        return {
            baseUrl: baseUrl,
            loginUrl: loginUrl,

            getToken: function () {
                return localStorage.getItem(TOKEN_KEY);
            },

            getRefreshToken: function () {
                return localStorage.getItem(REFRESH_KEY);
            },

            setTokens: function (accessToken, refreshToken) {
                if (accessToken) {
                    localStorage.setItem(TOKEN_KEY, accessToken);
                }
                if (refreshToken) {
                    localStorage.setItem(REFRESH_KEY, refreshToken);
                }
            },

            clearTokens: function () {
                localStorage.removeItem(TOKEN_KEY);
                localStorage.removeItem(REFRESH_KEY);
            },

            isAuthenticated: function () {
                return !!this.getToken();
            },

            // Kembalikan false bila user belum login (sekaligus mengalihkan ke /login).
            requireAuth: function () {
                if (!this.isAuthenticated()) {
                    window.location.href = loginUrl;

                    return false;
                }

                return true;
            },

            /**
             * Membungkus fetch() agar selalu mengembalikan
             * { ok, status, data } dan menangani sesi yang kedaluwarsa.
             */
            request: function (path, options) {
                options = options || {};

                var self = this;
                var headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                };

                if (options.auth) {
                    headers['Authorization'] = 'Bearer ' + self.getToken();
                }

                return fetch(baseUrl + path, {
                    method: options.method || 'POST',
                    headers: headers,
                    body: options.body ? JSON.stringify(options.body) : null,
                }).then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (data) {
                        if (response.status === 401 && options.auth) {
                            self.clearTokens();
                            window.location.href = loginUrl;

                            throw new Error('Sesi Anda berakhir. Silakan login kembali.');
                        }

                        return { ok: response.ok, status: response.status, data: data };
                    });
                });
            },
        };
    })();

    // Helper kecil untuk menampilkan notifikasi & memformat angka rupiah.
    window.WalletUI = {
        alert: function (container, type, message) {
            var element = typeof container === 'string' ? document.getElementById(container) : container;

            if (!element) {
                return;
            }

            var palette = {
                success: 'bg-emerald-50 text-emerald-800 border-emerald-200',
                error: 'bg-red-50 text-red-800 border-red-200',
                info: 'bg-sky-50 text-sky-800 border-sky-200',
            };

            element.className = 'mb-4 rounded-xl border px-4 py-3 text-sm ' + (palette[type] || palette.info);
            element.textContent = message;
            element.classList.remove('hidden');
        },

        clear: function (container) {
            var element = typeof container === 'string' ? document.getElementById(container) : container;

            if (!element) {
                return;
            }

            element.className = 'hidden';
            element.textContent = '';
        },

        rupiah: function (value) {
            return 'Rp ' + (Number(value) || 0).toLocaleString('id-ID');
        },

        // Mencegah data dari API dirender sebagai HTML mentah.
        escape: function (value) {
            var element = document.createElement('div');
            element.textContent = value === null || value === undefined ? '' : String(value);

            return element.innerHTML;
        },
    };
</script>