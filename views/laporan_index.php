<h4>📊 Laporan</h4>
<form method="get" class="row g-2 mb-3">
  <div class="col-auto"><label class="form-label small">Dari</label><input class="form-control" type="date" name="dari" value="<?= e($dari) ?>"></div>
  <div class="col-auto"><label class="form-label small">Sampai</label><input class="form-control" type="date" name="sampai" value="<?= e($sampai) ?>"></div>
  <div class="col-auto align-self-end"><button class="btn btn-primary" style="background:#8d5524;border:none">Tampilkan</button></div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card bg-primary"><div class="num"><?= (int)($summary['jml'] ?? 0) ?></div><div>Pesanan</div></div></div>
  <div class="col-md-4"><div class="stat-card bg-success"><div class="num"><?= rupiah((float)($summary['total'] ?? 0)) ?></div><div>Total Omzet</div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-header">Per Status</div>
    <div class="card-body py-2">
      <?php foreach ($perStatus as $s): ?>
        <div class="d-flex justify-content-between"><span><?= status_badge($s['status']) ?></span><b><?= $s['jml'] ?></b></div>
      <?php endforeach; ?>
    </div></div></div>
</div>

<div class="card"><div class="card-header">Penjualan per Produk</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Produk</th><th>Kategori</th><th>Qty Terjual</th><th>Total</th></tr></thead>
      <tbody>
      <?php foreach ($perProduk as $p): ?>
        <tr><td><?= e($p['nama']) ?></td><td><?= e($p['kategori']) ?></td>
          <td><?= $p['qty'] ?></td><td><?= rupiah((float)$p['total']) ?></td></tr>
      <?php endforeach; ?>
      <?php if ($perProduk === []): ?><tr><td colspan="4" class="text-muted">Tidak ada data periode ini.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div></div>