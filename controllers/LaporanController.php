<?php
declare(strict_types=1);

final class LaporanController extends Controller
{
    public function index(): void
    {
        $dari = $this->post('dari', $_GET['dari'] ?? date('Y-m-01'));
        $sampai = $this->post('sampai', $_GET['sampai'] ?? date('Y-m-d'));

        $summary = Database::one(
            'SELECT COUNT(*) jml, COALESCE(SUM(total),0) total
             FROM pesanan WHERE tanggal BETWEEN ? AND ? AND status != "batal"',
            [$dari, $sampai]
        );
        $perProduk = Database::all(
            'SELECT pr.nama, pr.kategori, SUM(pi.qty) qty, SUM(pi.qty * pi.harga) total
             FROM pesanan_item pi
             JOIN produk pr ON pr.id = pi.produk_id
             JOIN pesanan p ON p.id = pi.pesanan_id
             WHERE p.tanggal BETWEEN ? AND ? AND p.status != "batal"
             GROUP BY pr.id ORDER BY total DESC',
            [$dari, $sampai]
        );
        $perStatus = Database::all(
            'SELECT status, COUNT(*) jml FROM pesanan WHERE tanggal BETWEEN ? AND ? GROUP BY status',
            [$dari, $sampai]
        );
        $this->view('laporan_index', [
            'dari'    => $dari,
            'sampai'  => $sampai,
            'summary' => $summary,
            'perProduk' => $perProduk,
            'perStatus' => $perStatus,
            'title'   => 'Laporan',
            '_active' => 'laporan',
        ]);
    }
}