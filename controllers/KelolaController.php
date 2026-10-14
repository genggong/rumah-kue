<?php
declare(strict_types=1);

final class KelolaController extends Controller
{
    public function index(): void
    {
        $filter = $this->post('status', $_GET['status'] ?? '');
        $where  = $filter !== '' && $filter !== 'semua' ? 'WHERE p.status = ?' : '';
        $params = $filter !== '' && $filter !== 'semua' ? [$filter] : [];

        $pesanan = Database::all(
            "SELECT p.*, u.nama nama_pemesan, u.hp hp_pemesan,
                (SELECT COUNT(*) FROM pesanan_item pi WHERE pi.pesanan_id = p.id) jml_item
             FROM pesanan p JOIN users u ON u.id = p.pemesan_id
             $where
             ORDER BY CASE p.status WHEN 'baru' THEN 0 WHEN 'verifikasi' THEN 1 WHEN 'produksi' THEN 2 ELSE 3 END, p.tanggal ASC, p.jam ASC",
            $params
        );
        $this->view('kelola_index', [
            'pesanan' => $pesanan,
            'filter'  => $filter,
            'title'   => 'Kelola Pesanan',
            '_active' => 'kelola',
        ]);
    }

    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $pesanan = Database::one('SELECT p.*, u.nama nama_pemesan, u.hp hp_pemesan FROM pesanan p JOIN users u ON u.id = p.pemesan_id WHERE p.id = ?', [$id]);
        if ($pesanan === null) {
            Flash::set('error', 'Pesanan tidak ditemukan.');
            redirect('/');
        }
        $items = Database::all(
            'SELECT pi.*, pr.nama, pr.kategori FROM pesanan_item pi JOIN produk pr ON pr.id = pi.produk_id WHERE pi.pesanan_id = ?',
            [$id]
        );
        $timeline = Database::all('SELECT * FROM timeline WHERE pesanan_id = ? ORDER BY id ASC', [$id]);

        // Detail bahan baku yang dibutuhkan + cek ketersediaan stok
        $bahanKebutuhan = [];
        $stokCukup = true;
        foreach ($items as $item) {
            $resep = Database::all(
                'SELECT pb.qty, b.nama nama_bahan, b.satuan, b.stok, b.harga_satuan, b.id bahan_id
                 FROM produk_bahan pb JOIN bahan_baku b ON b.id = pb.bahan_id WHERE pb.produk_id = ?',
                [$item['produk_id']]
            );
            foreach ($resep as $r) {
                $butuh = (float)$r['qty'] * (float)$item['qty'];
                $key = $r['bahan_id'];
                if (!isset($bahanKebutuhan[$key])) {
                    $bahanKebutuhan[$key] = [
                        'nama'  => $r['nama_bahan'],
                        'satuan'=> $r['satuan'],
                        'stok'  => (float)$r['stok'],
                        'butuh' => 0.0,
                        'harga' => (float)$r['harga_satuan'],
                    ];
                }
                $bahanKebutuhan[$key]['butuh'] += $butuh;
            }
        }
        foreach ($bahanKebutuhan as &$bk) {
            if ($bk['stok'] < $bk['butuh']) {
                $stokCukup = false;
            }
        }
        unset($bk);

        $this->view('kelola_detail', [
            'pesanan'       => $pesanan,
            'items'         => $items,
            'timeline'      => $timeline,
            'bahanKebutuhan'=> $bahanKebutuhan,
            'stokCukup'     => $stokCukup,
            'title'         => 'Detail ' . $pesanan['no_pesanan'],
            '_active'       => 'kelola',
        ]);
    }

    /** Verifikasi pesanan: terima → status produksi + kurangi stok + timeline */
    public function terima(): void
    {
        csrf_verify();
        $id = (int)$this->post('id');
        $pesanan = Database::one('SELECT * FROM pesanan WHERE id = ?', [$id]);
        if ($pesanan === null || $pesanan['status'] !== 'baru') {
            Flash::set('error', 'Pesanan tidak valid atau sudah diproses.');
            redirect('/kelola');
        }

        $items = Database::all('SELECT * FROM pesanan_item WHERE pesanan_id = ?', [$id]);

        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            // Kurangi stok sesuai resep & catat mutasi keluar
            foreach ($items as $item) {
                $resep = Database::all(
                    'SELECT pb.qty, b.id bahan_id, b.nama nama_bahan FROM produk_bahan pb JOIN bahan_baku b ON b.id = pb.bahan_id WHERE pb.produk_id = ?',
                    [$item['produk_id']]
                );
                foreach ($resep as $r) {
                    $butuh = (float)$r['qty'] * (float)$item['qty'];
                    $b = Database::one('SELECT stok FROM bahan_baku WHERE id = ?', [$r['bahan_id']]);
                    $stokBaru = max(0.0, (float)($b['stok'] ?? 0) - $butuh);
                    Database::run('UPDATE bahan_baku SET stok = ?, updated_at = datetime("now","localtime") WHERE id = ?', [$stokBaru, $r['bahan_id']]);
                    Database::run('INSERT INTO stok_mutasi (bahan_id, pesanan_id, jenis, jumlah, catatan) VALUES (?,?,?,?,?)',
                        [$r['bahan_id'], $id, 'keluar', $butuh, 'Pesanan ' . $pesanan['no_pesanan']]);
                }
            }
            Database::run('UPDATE pesanan SET status = "produksi" WHERE id = ?', [$id]);
            Database::run('INSERT INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)',
                [$id, 'Diverifikasi & diterima', 'Bahan baku dikurangi, masuk antrian produksi']);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Gagal verifikasi: ' . $e->getMessage());
            redirect('/kelola');
        }
        Flash::set('success', 'Pesanan diterima & dikirim ke produksi. Stok bahan otomatis dikurangi.');
        redirect('/kelola');
    }

    public function tolak(): void
    {
        csrf_verify();
        $id    = (int)$this->post('id');
        $alasan = $this->post('alasan');
        Database::run('UPDATE pesanan SET status = "batal" WHERE id = ?', [$id]);
        Database::run('INSERT INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)',
            [$id, 'Ditolak admin', $alasan !== '' ? $alasan : 'Tidak ada keterangan']);
        Flash::set('success', 'Pesanan ditolak.');
        redirect('/kelola');
    }

    public function keProduksi(): void
    {
        csrf_verify();
        $id = (int)$this->post('id');
        $pesanan = Database::one('SELECT * FROM pesanan WHERE id = ?', [$id]);
        if ($pesanan === null || !in_array($pesanan['status'], ['baru', 'verifikasi'], true)) {
            Flash::set('error', 'Status tidak valid.');
            redirect('/kelola');
        }
        Database::run('UPDATE pesanan SET status = "produksi" WHERE id = ?', [$id]);
        Database::run('INSERT INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)',
            [$id, 'Masuk produksi', 'Menunggu pengerjaan sesuai jadwal']);
        Flash::set('success', 'Pesanan dikirim ke produksi.');
        redirect('/kelola');
    }
}