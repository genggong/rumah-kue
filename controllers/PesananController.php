<?php
declare(strict_types=1);

final class PesananController extends Controller
{
    public function form(): void
    {
        $produk = Database::all('SELECT * FROM produk WHERE aktif = 1 ORDER BY kategori, nama');
        $this->view('pesan_form', [
            'produk' => $produk,
            'title'  => 'Buat Pesanan',
            '_active' => 'pesan',
        ]);
    }

    public function simpan(): void
    {
        csrf_verify();
        $tanggal = $this->post('tanggal');
        $jam     = $this->post('jam');
        $catatan = $this->post('catatan');
        $items   = $_POST['produk'] ?? [];
        $qtys    = $_POST['qty'] ?? [];

        if ($tanggal === '' || $jam === '' || !is_array($items) || $items === []) {
            Flash::set('error', 'Tanggal, jam, dan minimal 1 item wajib diisi.');
            redirect('/pesan');
        }
        if (is_array($qtys) && count($qtys) > 0) {
            // normalisasi: hanya item dengan qty > 0
        }

        $pdo = Database::conn();
        $no  = 'RK-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $total = 0.0;
        $detail = [];

        foreach ($items as $i => $produkId) {
            $produkId = (int)$produkId;
            $qty = (float)($qtys[$i] ?? 1);
            if ($produkId <= 0 || $qty <= 0) {
                continue;
            }
            $p = Database::one('SELECT * FROM produk WHERE id = ? AND aktif = 1', [$produkId]);
            if ($p === null) {
                continue;
            }
            $total += (float)$p['harga_jual'] * $qty;
            $detail[] = ['produk' => $p, 'qty' => $qty];
        }
        if ($detail === []) {
            Flash::set('error', 'Tidak ada item valid yang dipilih.');
            redirect('/pesan');
        }

        $pdo->beginTransaction();
        try {
            $pesananId = Database::run(
                'INSERT INTO pesanan (no_pesanan, pemesan_id, tanggal, jam, status, catatan, total) VALUES (?,?,?,?,?,?,?)',
                [$no, Auth::id(), $tanggal, $jam, 'baru', $catatan, $total]
            );
            foreach ($detail as $d) {
                Database::run(
                    'INSERT INTO pesanan_item (pesanan_id, produk_id, qty, harga) VALUES (?,?,?,?)',
                    [$pesananId, $d['produk']['id'], $d['qty'], $d['produk']['harga_jual']]
                );
            }
            Database::run('INSERT INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)',
                [$pesananId, 'Pesanan dibuat', 'Menunggu verifikasi admin']);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Gagal menyimpan pesanan: ' . $e->getMessage());
            redirect('/pesan');
        }

        Flash::set('success', 'Pesanan ' . $no . ' berhasil dibuat. Menunggu verifikasi admin.');
        redirect('/pesanan/saya');
    }

    public function riwayat(): void
    {
        $pesanan = Database::all(
            'SELECT p.*, (SELECT COUNT(*) FROM pesanan_item pi WHERE pi.pesanan_id = p.id) jml_item
             FROM pesanan p WHERE p.pemesan_id = ? ORDER BY p.id DESC',
            [Auth::id()]
        );
        $this->view('pesanan_riwayat', [
            'pesanan' => $pesanan,
            'title'   => 'Riwayat Pesanan Saya',
            '_active' => 'riwayat',
        ]);
    }

    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $role = Auth::role();
        $pesanan = Database::one('SELECT * FROM pesanan WHERE id = ?', [$id]);
        if ($pesanan === null) {
            Flash::set('error', 'Pesanan tidak ditemukan.');
            redirect('/');
        }
        if ($role === 'pemesan' && (int)$pesanan['pemesan_id'] !== Auth::id()) {
            Flash::set('error', 'Anda tidak punya akses.');
            redirect('/pesanan/saya');
        }
        $items = Database::all(
            'SELECT pi.*, pr.nama, pr.kategori FROM pesanan_item pi JOIN produk pr ON pr.id = pi.produk_id WHERE pi.pesanan_id = ?',
            [$id]
        );
        $timeline = Database::all('SELECT * FROM timeline WHERE pesanan_id = ? ORDER BY id ASC', [$id]);
        $this->view('pesanan_detail', [
            'pesanan'  => $pesanan,
            'items'    => $items,
            'timeline' => $timeline,
            'title'    => 'Detail Pesanan ' . $pesanan['no_pesanan'],
        ]);
    }
}