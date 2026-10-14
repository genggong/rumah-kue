<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">🧾 <?= e($pesanan['no_pesanan']) ?></h4>
  <div><?= status_badge($pesanan['status']) ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/kelola">← Kembali</a></div>
</div>

<div class="alert alert-light border">
  <strong><?= e($pesanan['nama_pemesan']) ?></strong> — <?= format_hari($pesanan['tanggal']) ?> pukul <strong><?= e($pesanan['jam']) ?></strong>
  <?php if ($pesanan['hp_pemesan'] !== ''): ?> • 📞 <?= e($pesanan['hp_pemesan']) ?><?php endif; ?>
</div>

<div class="row g-3">
  <div class="col-md-7">
    <div class="card mb-3"><div class="card-header">Item Pesanan</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>Produk</th><th>Kategori</th><th>Qty</th><th>Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr><td><?= e($it['nama']) ?></td><td><?= e($it['kategori']) ?></td>
              <td><?= $it['qty'] ?></td><td><?= rupiah((float)$it['qty']*(float)$it['harga']) ?></td></tr>
          <?php endforeach; ?>
          </tbody><tfoot><tr><th colspan="3" class="text-end">Total</th><th><?= rupiah((float)$pesanan['total']) ?></th></tr></tfoot>
        </table>
      </div></div>

    <div class="card"><div class="card-header">🧪 Kebutuhan Bahan Baku & Cek Stok</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>Bahan</th><th>Butuh</th><th>Stok</th><th>Status</th><th>Nilai</th></tr></thead>
          <tbody>
          <?php foreach ($bahanKebutuhan as $bk): $cukup = $bk['stok'] >= $bk['butuh']; ?>
            <tr class="<?= $cukup ? '' : 'table-danger' ?>">
              <td><?= e($bk['nama']) ?></td>
              <td><?= $bk['butuh'] ?> <?= e($bk['satuan']) ?></td>
              <td><?= $bk['stok'] ?> <?= e($bk['satuan']) ?></td>
              <td><?= $cukup ? '<span class="badge bg-success">Cukup</span>' : '<span class="badge bg-danger">KURANG</span>' ?></td>
              <td><?= rupiah($bk['butuh'] * $bk['harga']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($bahanKebutuhan === []): ?><tr><td colspan="5" class="text-muted">Tidak ada resep bahan untuk item ini.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-body border-top">
        <?php if ($stokCukup): ?><span class="badge bg-success mb-1">✓ Semua bahan tersedia</span>
        <?php else: ?><span class="badge bg-danger mb-1">✗ Ada bahan kurang — beli dulu sebelum produksi</span><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card mb-3"><div class="card-header">Aksi Admin</div>
      <div class="card-body">
        <?php if ($pesanan['status'] === 'baru' && $stokCukup): ?>
          <form method="post" action="<?= BASE_URL ?>/kelola/terima" class="mb-2">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
            <button class="btn btn-success w-100">✓ Terima & Kirim ke Produksi (kurangi stok)</button>
          </form>
        <?php endif; ?>
        <?php if (in_array($pesanan['status'], ['baru','verifikasi'], true)): ?>
          <form method="post" action="<?= BASE_URL ?>/kelola/keProduksi" class="mb-2">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
            <button class="btn btn-primary w-100" style="background:#8d5524;border:none">Kirim ke Produksi (tanpa kurangi stok)</button>
          </form>
        <?php endif; ?>
        <?php if ($pesanan['status'] === 'baru'): ?>
          <form method="post" action="<?= BASE_URL ?>/kelola/tolak">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
            <div class="input-group"><input class="form-control" name="alasan" placeholder="Alasan tolak (opsional)">
              <button class="btn btn-outline-danger">Tolak</button></div>
          </form>
        <?php endif; ?>
      </div></div>

    <div class="card"><div class="card-header">🔄 Timeline</div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php foreach ($timeline as $t): ?>
            <li class="list-group-item"><div class="fw-bold"><?= e($t['tahap']) ?></div>
              <div class="small text-muted"><?= e($t['catatan']) ?><br><?= e($t['created_at']) ?></div></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
  </div>
</div>