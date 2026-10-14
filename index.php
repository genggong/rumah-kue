<?php
/**
 * Aplikasi Rumah Kue — Manajemen Pesanan Kue & Paket Nasi
 * Arsitektur: PHP native + SQLite (db/rumahkue.db), tanpa dependensi eksternal.
 *
 * Alur:
 *  - Pemesan  : buat pesanan (kue/paket nasi) dengan tanggal & jam pengambilan
 *  - Admin    : verifikasi pesanan + cek stok bahan baku + teruskan ke produksi
 *  - Produksi : kerjakan sesuai timeline (hari & jam pesanan)
 *
 * Keamanan: PDO prepared statement, password_hash, session regenerate,
 * CSRF token, output escaping.
 */
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Makassar');

define('BASE_PATH', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') ?: '');

require BASE_PATH . '/core/Database.php';
require BASE_PATH . '/core/Auth.php';
require BASE_PATH . '/core/Helper.php';
require BASE_PATH . '/core/Controller.php';
require BASE_PATH . '/core/Router.php';

foreach (glob(BASE_PATH . '/controllers/*.php') ?: [] as $file) {
    require_once $file;
}

Router::dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));