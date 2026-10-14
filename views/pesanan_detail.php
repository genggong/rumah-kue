<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">🧾 <?= e($pesanan['no_pesanan']) ?></h4>
  <?= status_badge($pesanan['status']) ?>
</div>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card mb-3"><div class="card-header">Item Pesanan</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>Produk</th><th>Kategori</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr><td><?= e($it['nama']) ?></td><td><?= e($it['kategori']) ?></td>
              <td><?= $it['qty'] ?></td><td><?= rupiah((float)$it['harga']) ?></td>
              <td><?= rupiah((float)$it['qty'] * (float)$it['harga']) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot><tr><th colspan="4" class="text-end">Total</th><th><?= rupiah((float)$pesanan['total']) ?></th></tr></tfoot>
        </table>
      </div></div>
    <div class="card"><div class="card-header">⏰ Jadwal Pengambilan</div>
      <div class="card-body">
        <strong><?= format_hari($pesanan['tanggal']) ?></strong> pukul <strong><?= e($pesanan['jam']) ?> (WITA)</strong>
        <?php if ($pesanan['catatan'] !== ''): ?>
          <div class="mt-2 p-2 bg-light rounded"><strong>Catatan:</strong> <?= e($pesanan['catatan']) ?></div>
        <?php endif; ?>
      </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-header">🔄 Timeline</div>
      <div class="card-body p-0">
        <?php if ($timeline === []): ?><div class="p-3 text-muted">Belum ada riwayat.</div>
        <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($timeline as $t): ?>
            <li class="list-group-item"><div class="fw-bold"><?= e($t['tahap']) ?></div>
              <div class="small text-muted"><?= e($t['catatan']) ?><br><?= e($t['created_at']) ?></div></li>
          <?php endforeach; ?>
        </ul><?php endif; ?>
      </div></div>
  </div>
</div>