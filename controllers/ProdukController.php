<?php
declare(strict_types=1);

final class ProdukController extends Controller
{
    public function index(): void
    {
        $produk = Database::all(
            'SELECT p.*,
                (SELECT COUNT(*) FROM produk_bahan pb WHERE pb.produk_id = p.id) jml_bahan
             FROM produk p ORDER BY p.kategori, p.nama'
        );
        $this->view('produk_index', [
            'produk' => $produk,
            'title'  => 'Produk',
            '_active' => 'produk',
        ]);
    }

    public function tambah(): void
    {
        csrf_verify();
        $nama     = $this->post('nama');
        $kategori = $this->post('kategori', 'KUE');
        $harga    = $this->postFloat('harga_jual');

        if ($nama === '' || !in_array($kategori, ['KUE', 'NASI'], true) || $harga <= 0) {
            Flash::set('error', 'Nama, kategori, dan harga (valid) wajib diisi.');
            redirect('/produk');
        }
        Database::run(
            'INSERT INTO produk (nama, kategori, harga_jual, aktif) VALUES (?,?,?,1)',
            [$nama, $kategori, $harga]
        );
        Flash::set('success', 'Produk "' . e($nama) . '" ditambahkan.');
        redirect('/produk');
    }

    public function resep(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $produk = $id > 0 ? Database::one('SELECT * FROM produk WHERE id = ?', [$id]) : null;
        if ($produk === null) {
            Flash::set('error', 'Produk tidak ditemukan.');
            redirect('/produk');
        }
        $bahan       = Database::all('SELECT * FROM bahan_baku ORDER BY nama');
        $resep       = Database::all(
            'SELECT pb.*, b.nama nama_bahan, b.satuan FROM produk_bahan pb JOIN bahan_baku b ON b.id = pb.bahan_id WHERE pb.produk_id = ? ORDER BY b.nama',
            [$id]
        );
        // Estimasi biaya bahan per produk (untuk harga jual)
        $biayaBahan = 0.0;
        foreach ($resep as $r) {
            $biayaBahan += (float)$r['qty'] * (float)(Database::one('SELECT harga_satuan h FROM bahan_baku WHERE id = ?', [$r['bahan_id']])['h'] ?? 0);
        }
        $this->view('produk_resep', [
            'produk'    => $produk,
            'bahan'     => $bahan,
            'resep'     => $resep,
            'biayaBahan'=> $biayaBahan,
            'title'     => 'Resep ' . $produk['nama'],
            '_active'   => 'produk',
        ]);
    }

    public function resepSimpan(): void
    {
        csrf_verify();
        $produkId = (int)$this->post('produk_id');
        if ($produkId <= 0) {
            redirect('/produk');
        }
        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            Database::run('DELETE FROM produk_bahan WHERE produk_id = ?', [$produkId]);
            $bahanIds = $_POST['bahan'] ?? [];
            $qtys     = $_POST['qty'] ?? [];
            if (is_array($bahanIds)) {
                foreach ($bahanIds as $i => $bahanId) {
                    $bahanId = (int)$bahanId;
                    $qty = (float)($qtys[$i] ?? 0);
                    if ($bahanId > 0 && $qty > 0) {
                        Database::run('INSERT INTO produk_bahan (produk_id, bahan_id, qty) VALUES (?,?,?)', [$produkId, $bahanId, $qty]);
                    }
                }
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Gagal simpan resep.');
            redirect('/produk/resep?id=' . $produkId);
        }
        Flash::set('success', 'Resep produk disimpan.');
        redirect('/produk/resep?id=' . $produkId);
    }

    public function hapus(): void
    {
        csrf_verify();
        $id = (int)$this->post('id');
        Database::run('DELETE FROM produk WHERE id = ?', [$id]);
        Flash::set('success', 'Produk dihapus.');
        redirect('/produk');
    }
}