<?php
// Seed data demo untuk evaluasi UI (dijalankan sekali via CLI)
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');
define('BASE_PATH', __DIR__);
require __DIR__ . '/core/Database.php';

$pdo = Database::conn();

// Bahan baku
$bahan = [
    ['Tepung Terigu', 'kg', 50, 12000, 10],
    ['Telur', 'butir', 100, 2500, 20],
    ['Gula Pasir', 'kg', 30, 16000, 8],
    ['Mentega', 'kg', 15, 28000, 5],
    ['Beras', 'kg', 40, 15000, 10],
    ['Ayam Fillet', 'kg', 12, 38000, 4],
    ['Bumbu Instan Nasi Goreng', 'bungkus', 60, 4500, 15],
    ['Chocolate Chips', 'kg', 8, 55000, 3],
];
$stmt = $pdo->prepare('INSERT OR IGNORE INTO bahan_baku (nama, satuan, stok, harga_satuan, min_stok) VALUES (?,?,?,?,?)');
foreach ($bahan as $b) { $stmt->execute($b); }

// Produk
$produk = [
    ['Kue Bolu Coklat', 'KUE', 50000],
    ['Kue Black Forest', 'KUE', 85000],
    ['Kue Ulang Tahun (1 loyang)', 'KUE', 150000],
    ['Paket Nasi Ayam', 'NASI', 25000],
    ['Paket Nasi Goreng Spesial', 'NASI', 30000],
    ['Paket Nasi Kotak (Box)', 'NASI', 22000],
];
$pstmt = $pdo->prepare('INSERT OR IGNORE INTO produk (nama, kategori, harga_jual, aktif) VALUES (?,?,?,1)');
foreach ($produk as $p) { $pstmt->execute($p); }

// Resep: produk_id => [(bahan_id, qty)]
$resep = [
    1 => [[1, 0.5], [2, 3], [3, 0.3], [4, 0.2], [8, 0.1]],
    2 => [[1, 0.4], [2, 3], [3, 0.25], [8, 0.2]],
    3 => [[1, 1.2], [2, 6], [3, 0.8], [4, 0.5], [8, 0.4]],
    4 => [[5, 0.3], [6, 0.2], [7, 1]],
    5 => [[5, 0.35], [6, 0.2], [7, 1]],
    6 => [[5, 0.25], [6, 0.15], [7, 1]],
];
foreach ($resep as $pid => $items) {
    foreach ($items as [$bid, $qty]) {
        $pdo->prepare('INSERT OR IGNORE INTO produk_bahan (produk_id, bahan_id, qty) VALUES (?,?,?)')->execute([$pid, $bid, $qty]);
    }
}

// Pemesan contoh tambahan
$users = [
    ['budi', 'Pemesan Budi', '081234567801'],
    ['sari', 'Pemesan Sari', '081234567802'],
];
foreach ($users as [$u, $n, $hp]) {
    $pdo->prepare('INSERT OR IGNORE INTO users (username, password, role, nama, hp) VALUES (?,?,?,?,?)')
        ->execute([$u, password_hash('pemesan123', PASSWORD_DEFAULT), 'pemesan', $n, $hp]);
}

// Pesanan demo (dari pemesan id 3 = pemesan, id 4 = budi, id 5 = sari)
$pemesanId = (int)$pdo->query('SELECT id FROM users WHERE username="pemesan"')->fetch()['id'];
$budiId   = (int)$pdo->query('SELECT id FROM users WHERE username="budi"')->fetch()['id'];

$pesanan = [
    [$pemesanId, date('Y-m-d', strtotime('+1 day')), '10:30', 'produksi', 'Tulis "Selamat Ulang Tahun" di atas kue', 185000],
    [$budiId, date('Y-m-d', strtotime('+2 day')), '08:00', 'baru', 'Pesan untuk acara kantor, 10 box', 220000],
    [$pemesanId, date('Y-m-d', strtotime('-2 day')), '14:00', 'selesai', '', 100000],
];
foreach ($pesanan as [$pid, $tgl, $jam, $st, $cat, $total]) {
    $no = 'RK-' . date('ymd', strtotime($tgl)) . '-' . strtoupper(substr(md5((string)$pid . $tgl . $jam), 0, 4));
    $pdo->prepare('INSERT OR IGNORE INTO pesanan (no_pesanan, pemesan_id, tanggal, jam, status, catatan, total) VALUES (?,?,?,?,?,?,?)')
        ->execute([$no, $pid, $tgl, $jam, $st, $cat, $total]);
}
$ps = $pdo->query('SELECT id, no_pesanan FROM pesanan')->fetchAll();
$itemDef = [
    [1, 1, 1, 50000],
    [1, 4, 3, 25000],
    [2, 6, 10, 22000],
    [3, 1, 2, 50000],
];
foreach ($ps as $i => $row) {
    $def = $itemDef[$i % count($itemDef)];
    $pdo->prepare('INSERT OR IGNORE INTO pesanan_item (pesanan_id, produk_id, qty, harga) VALUES (?,?,?,?)')
        ->execute([$row['id'], $def[1], $def[2], $def[3]]);
    $pdo->prepare('INSERT OR IGNORE INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)')
        ->execute([$row['id'], 'Pesanan dibuat', 'Menunggu verifikasi admin']);
}

echo "Seed demo selesai: " . count($bahan) . " bahan, " . count($produk) . " produk, " . count($pesanan) . " pesanan\n";