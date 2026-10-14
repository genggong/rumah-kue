<?php
declare(strict_types=1);

final class Router
{
    private static array $routes = [
        ['GET',  '/login',                  'AuthController',   'loginForm',  ['guest']],
        ['POST', '/login',                  'AuthController',   'login',      ['guest']],
        ['GET',  '/logout',                 'AuthController',   'logout',     ['*']],
        ['GET',  '/',                       'DashboardController', 'index',   ['*']],

        // ---- Pemesan ----
        ['GET',  '/pesan',                  'PesananController',  'form',      ['pemesan']],
        ['POST', '/pesan/simpan',           'PesananController',  'simpan',    ['pemesan']],
        ['GET',  '/pesanan/saya',           'PesananController',  'riwayat',   ['pemesan']],
        ['GET',  '/pesanan/saya/detail',    'PesananController',  'detail',    ['pemesan','admin','produksi']],

        // ---- Admin: bahan baku ----
        ['GET',  '/bahan',                  'BahanController',    'index',     ['admin']],
        ['POST', '/bahan/tambah',           'BahanController',    'tambah',    ['admin']],
        ['POST', '/bahan/ubah',             'BahanController',    'ubah',      ['admin']],
        ['POST', '/bahan/stok',             'BahanController',    'mutasi',    ['admin']],
        ['POST', '/bahan/hapus',            'BahanController',    'hapus',     ['admin']],

        // ---- Admin: produk & resep ----
        ['GET',  '/produk',                 'ProdukController',   'index',     ['admin']],
        ['POST', '/produk/tambah',          'ProdukController',   'tambah',    ['admin']],
        ['GET',  '/produk/resep',           'ProdukController',   'resep',     ['admin']],
        ['POST', '/produk/resep/simpan',    'ProdukController',   'resepSimpan', ['admin']],
        ['POST', '/produk/hapus',           'ProdukController',   'hapus',     ['admin']],

        // ---- Admin: kelola pesanan ----
        ['GET',  '/kelola',                 'KelolaController',   'index',     ['admin']],
        ['GET',  '/kelola/detail',          'KelolaController',   'detail',    ['admin','produksi']],
        ['POST', '/kelola/terima',          'KelolaController',   'terima',    ['admin']],
        ['POST', '/kelola/tolak',           'KelolaController',   'tolak',     ['admin']],
        ['POST', '/kelola/produksi',        'KelolaController',   'keProduksi',['admin']],

        // ---- Produksi ----
        ['GET',  '/produksi',               'ProduksiController', 'antrian',   ['produksi','admin']],
        ['POST', '/produksi/selesai',       'ProduksiController', 'selesai',   ['produksi','admin']],

        // ---- Pengguna ----
        ['GET',  '/pengguna',               'PenggunaController', 'index',     ['admin']],
        ['POST', '/pengguna/tambah',        'PenggunaController', 'tambah',    ['admin']],

        // ---- Laporan ----
        ['GET',  '/laporan',                'LaporanController',  'index',     ['admin']],
    ];

    public static function dispatch(string $method, string $path): void
    {
        Auth::start();
        Flash::start();
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach (self::$routes as [$m, $pattern, $ctrl, $action, $roles]) {
            if ($m !== $method) {
                continue;
            }
            if (!preg_match('#^' . $pattern . '$#', $path)) {
                continue;
            }
            if (in_array('guest', $roles, true)) {
                if (Auth::check()) {
                    redirect('/');
                }
            } else {
                if (!Auth::check()) {
                    redirect('/login');
                }
                if (!in_array('*', $roles, true) && !in_array(Auth::role(), $roles, true)) {
                    Flash::set('error', 'Anda tidak punya akses ke halaman ini.');
                    redirect('/');
                }
            }

            $class = $ctrl;
            if (!class_exists($class)) {
                http_response_code(500);
                exit('Controller tidak ditemukan: ' . $class);
            }
            $instance = new $class();
            if (!method_exists($instance, $action)) {
                http_response_code(500);
                exit('Action tidak ditemukan: ' . $action);
            }
            $instance->$action();
            return;
        }

        http_response_code(404);
        echo '404 - Halaman tidak ditemukan.';
    }
}