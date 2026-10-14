<?php
declare(strict_types=1);

final class BahanController extends Controller
{
    public function index(): void
    {
        $bahan = Database::all('SELECT * FROM bahan_baku ORDER BY nama');
        $this->view('bahan_index', [
            'bahan'  => $bahan,
            'title'  => 'Bahan Baku',
            '_active' => 'bahan',
        ]);
    }

    public function tambah(): void
    {
        csrf_verify();
        $nama  = $this->post('nama');
        $satuan = $this->post('satuan', 'pcs');
        $stok  = $this->postFloat('stok');
        $harga = $this->postFloat('harga_satuan');
        $min   = $this->postFloat('min_stok');

        if ($nama === '') {
            Flash::set('error', 'Nama bahan wajib diisi.');
            redirect('/bahan');
        }
        try {
            Database::run(
                'INSERT INTO bahan_baku (nama, satuan, stok, harga_satuan, min_stok, updated_at) VALUES (?,?,?,?,?, datetime("now","localtime"))',
                [$nama, $satuan, $stok, $harga, $min]
            );
        } catch (\PDOException $e) {
            Flash::set('error', 'Nama bahan sudah ada.');
            redirect('/bahan');
        }
        Flash::set('success', 'Bahan baku "' . e($nama) . '" ditambahkan.');
        redirect('/bahan');
    }

    public function ubah(): void
    {
        csrf_verify();
        $id    = (int)$this->post('id');
        $nama  = $this->post('nama');
        $satuan = $this->post('satuan');
        $harga = $this->postFloat('harga_satuan');
        $min   = $this->postFloat('min_stok');

        if ($id <= 0 || $nama === '') {
            Flash::set('error', 'Data tidak valid.');
            redirect('/bahan');
        }
        Database::run(
            'UPDATE bahan_baku SET nama = ?, satuan = ?, harga_satuan = ?, min_stok = ?, updated_at = datetime("now","localtime") WHERE id = ?',
            [$nama, $satuan, $harga, $min, $id]
        );
        Flash::set('success', 'Bahan baku diperbarui.');
        redirect('/bahan');
    }

    public function mutasi(): void
    {
        csrf_verify();
        $id     = (int)$this->post('id');
        $jenis  = $this->post('jenis', 'masuk');
        $jumlah = $this->postFloat('jumlah');
        $catatan = $this->post('catatan');

        if ($id <= 0 || $jumlah <= 0 || !in_array($jenis, ['masuk', 'keluar', 'koreksi'], true)) {
            Flash::set('error', 'Data mutasi tidak valid.');
            redirect('/bahan');
        }
        $b = Database::one('SELECT * FROM bahan_baku WHERE id = ?', [$id]);
        if ($b === null) {
            Flash::set('error', 'Bahan tidak ditemukan.');
            redirect('/bahan');
        }
        $stokBaru = match ($jenis) {
            'masuk'  => (float)$b['stok'] + $jumlah,
            'keluar' => max(0.0, (float)$b['stok'] - $jumlah),
            default  => $jumlah,
        };
        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            Database::run('UPDATE bahan_baku SET stok = ?, updated_at = datetime("now","localtime") WHERE id = ?', [$stokBaru, $id]);
            Database::run('INSERT INTO stok_mutasi (bahan_id, jenis, jumlah, catatan) VALUES (?,?,?,?)',
                [$id, $jenis, $jumlah, $catatan]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Gagal mutasi stok.');
            redirect('/bahan');
        }
        Flash::set('success', 'Stok ' . e($b['nama']) . ' = ' . $stokBaru . ' ' . e($b['satuan']) . '.');
        redirect('/bahan');
    }

    public function hapus(): void
    {
        csrf_verify();
        $id = (int)$this->post('id');
        Database::run('DELETE FROM bahan_baku WHERE id = ?', [$id]);
        Flash::set('success', 'Bahan baku dihapus.');
        redirect('/bahan');
    }
}