<?php
declare(strict_types=1);

final class ProduksiController extends Controller
{
    public function antrian(): void
    {
        // Antrian pesanan produksi, urut timeline: tanggal & jam pemesanan
        $antrian = Database::all(
            'SELECT p.*, u.nama nama_pemesan, u.hp hp_pemesan,
                (SELECT COUNT(*) FROM pesanan_item pi WHERE pi.pesanan_id = p.id) jml_item
             FROM pesanan p JOIN users u ON u.id = p.pemesan_id
             WHERE p.status = "produksi"
             ORDER BY p.tanggal ASC, p.jam ASC'
        );
        // Tambahkan detail item untuk tiap pesanan
        foreach ($antrian as &$a) {
            $a['items'] = Database::all(
                'SELECT pi.*, pr.nama, pr.kategori FROM pesanan_item pi JOIN produk pr ON pr.id = pi.produk_id WHERE pi.pesanan_id = ?',
                [(int)$a['id']]
            );
        }
        unset($a);

        // Timeline linear untuk semalam: urutan pengerjaan berdasarkan hari & jam
        $timeline = [];
        foreach ($antrian as $a) {
            $timeline[] = [
                'no'       => $a['no_pesanan'],
                'tanggal'  => $a['tanggal'],
                'jam'      => $a['jam'],
                'pemesan'  => $a['nama_pemesan'],
                'items'    => count($a['items']),
            ];
        }
        usort($timeline, fn($x, $y) => [$x['tanggal'], $x['jam']] <=> [$y['tanggal'], $y['jam']]);

        $this->view('produksi_antrian', [
            'antrian'  => $antrian,
            'timeline' => $timeline,
            'title'    => 'Antrian Produksi',
            '_active'  => 'produksi',
        ]);
    }

    public function selesai(): void
    {
        csrf_verify();
        $id = (int)$this->post('id');
        $pesanan = Database::one('SELECT * FROM pesanan WHERE id = ?', [$id]);
        if ($pesanan === null || $pesanan['status'] !== 'produksi') {
            Flash::set('error', 'Pesanan tidak valid.');
            redirect('/produksi');
        }
        Database::run('UPDATE pesanan SET status = "selesai" WHERE id = ?', [$id]);
        Database::run('INSERT INTO timeline (pesanan_id, tahap, catatan) VALUES (?,?,?)',
            [$id, 'Pesanan selesai', 'Siap diambil sesuai jadwal']);
        Flash::set('success', 'Pesanan ' . $pesanan['no_pesanan'] . ' ditandai selesai.');
        redirect('/produksi');
    }
}