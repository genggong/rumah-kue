<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $role = Auth::role();
        if ($role === 'pemesan') {
            $pesanan = Database::all(
                'SELECT * FROM pesanan WHERE pemesan_id = ? ORDER BY id DESC LIMIT 10',
                [Auth::id()]
            );
            $this->view('dashboard_pemesan', ['pesanan' => $pesanan, 'title' => 'Dashboard Pemesan']);
            return;
        }

        $data = [
            'jumlah_pesanan'   => (int)(Database::one('SELECT COUNT(*) c FROM pesanan')['c'] ?? 0),
            'pesanan_baru'     => (int)(Database::one('SELECT COUNT(*) c FROM pesanan WHERE status="baru"')['c'] ?? 0),
            'pesanan_produksi' => (int)(Database::one('SELECT COUNT(*) c FROM pesanan WHERE status="produksi"')['c'] ?? 0),
            'pesanan_hari_ini' => (int)(Database::one('SELECT COUNT(*) c FROM pesanan WHERE tanggal = ?', [date('Y-m-d')])['c'] ?? 0),
            'bahan_menipis'    => Database::all('SELECT * FROM bahan_baku WHERE stok <= min_stok ORDER BY stok ASC'),
            'antrian_produksi' => Database::all('SELECT * FROM pesanan WHERE status="produksi" ORDER BY tanggal ASC, jam ASC'),
            'title'            => 'Dashboard',
        ];
        $this->view('dashboard', $data);
    }
}