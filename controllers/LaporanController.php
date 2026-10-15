<?php
declare(strict_types=1);

final class LaporanController extends Controller
{
    public function index(): void
    {
        $dari   = $this->post('dari', $_GET['dari'] ?? date('Y-m-01'));
        $sampai = $this->post('sampai', $_GET['sampai'] ?? date('Y-m-d'));

        // Validasi rentang tanggal (anti tanggal tidak valid)
        if (!strtotime($dari) || !strtotime($sampai) || $sampai < $dari) {
            flash('error', 'Rentang tanggal tidak valid.');
            $dari = date('Y-m-01');
            $sampai = date('Y-m-d');
        }

        // Ringkasan: jumlah pesanan, omzet (non-batal), pesanan selesai
        $summary = Database::one(
            'SELECT COUNT(*) jml,
                    COALESCE(SUM(CASE WHEN status != "batal" THEN total ELSE 0 END),0) omzet,
                    SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) selesai
             FROM pesanan WHERE tanggal BETWEEN ? AND ?',
            [$dari, $sampai]
        );

        // Per produk: qty, omzet, HPP (biaya bahan dari resep), laba, margin
        $perProduk = Database::all(
            'SELECT pr.id, pr.nama, pr.kategori,
                    SUM(pi.qty) qty,
                    SUM(pi.qty * pi.harga) omzet,
                    SUM(pi.qty * COALESCE((
                        SELECT SUM(pb.qty * b.harga_satuan)
                        FROM produk_bahan pb
                        JOIN bahan_baku b ON b.id = pb.bahan_id
                        WHERE pb.produk_id = pi.produk_id
                    ),0)) hpp
             FROM pesanan_item pi
             JOIN produk pr ON pr.id = pi.produk_id
             JOIN pesanan p ON p.id = pi.pesanan_id
             WHERE p.tanggal BETWEEN ? AND ? AND p.status != "batal"
             GROUP BY pr.id ORDER BY omzet DESC',
            [$dari, $sampai]
        );

        // Total HPP & laba kotor
        $totalHpp = 0.0;
        foreach ($perProduk as $p) $totalHpp += (float)$p['hpp'];
        $omzet    = (float)($summary['omzet'] ?? 0);
        $laba     = $omzet - $totalHpp;
        $margin   = ($omzet > 0) ? ($laba / $omzet * 100.0) : 0.0;

        // Pengeluaran belanja bahan baku (stok mutasi masuk) pada periode
        $belanjaBahan = Database::one(
            'SELECT COALESCE(SUM(sm.jumlah * b.harga_satuan),0) total,
                    COUNT(*) jml
             FROM stok_mutasi sm
             JOIN bahan_baku b ON b.id = sm.bahan_id
             WHERE sm.jenis = "masuk" AND DATE(sm.created_at) BETWEEN ? AND ?',
            [$dari, $sampai]
        );

        // Per status
        $perStatus = Database::all(
            'SELECT status, COUNT(*) jml FROM pesanan WHERE tanggal BETWEEN ? AND ? GROUP BY status',
            [$dari, $sampai]
        );

        $this->view('laporan_index', [
            'dari'     => $dari,
            'sampai'   => $sampai,
            'summary'  => $summary,
            'perProduk'=> $perProduk,
            'totalHpp' => $totalHpp,
            'laba'     => $laba,
            'margin'   => $margin,
            'belanjaBahan' => $belanjaBahan,
            'perStatus'=> $perStatus,
            'title'    => 'Laporan Keuangan',
            '_active'  => 'laporan',
        ]);
    }
}