<h4>📊 Laporan Keuangan</h4>
<form method="get" class="row g-2 mb-3">
  <div class="col-auto"><label class="form-label small">Dari</label><input class="form-control" type="date" name="dari" value="<?= e($dari) ?>"></div>
  <div class="col-auto"><label class="form-label small">Sampai</label><input class="form-control" type="date" name="sampai" value="<?= e($sampai) ?>"></div>
  <div class="col-auto align-self-end"><button class="btn btn-primary" style="background:#8d5524;border:none">Tampilkan</button></div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card bg-primary"><div class="num"><?= (int)($summary['jml'] ?? 0) ?></div><div>Pesanan</div></div></div>
  <div class="col-md-3"><div class="stat-card bg-success"><div class="num"><?= rupiah((float)($summary['omzet'] ?? 0)) ?></div><div>Omzet (Non-Batal)</div></div></div>
  <div class="col-md-3"><div class="stat-card bg-info"><div class="num"><?= rupiah($laba) ?></div><div>Laba Kotor</div></div></div>
  <div class="col-md-3"><div class="stat-card bg-warning"><div class="num"><?= number_format($margin, 1, ',', '.') ?>%</div><div>Margin</div></div></div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card h-100"><div class="card-header">Rincian Keuangan</div>
    <div class="card-body py-2">
      <div class="d-flex justify-content-between mb-1"><span>Omzet</span><b class="text-success"><?= rupiah((float)($summary['omzet'] ?? 0)) ?></b></div>
      <div class="d-flex justify-content-between mb-1"><span>HPP (Biaya Bahan)</span><b class="text-danger">− <?= rupiah($totalHpp) ?></b></div>
      <hr class="my-1">
      <div class="d-flex justify-content-between mb-1"><span><b>Laba Kotor</b></span><b><?= rupiah($laba) ?></b></div>
      <div class="d-flex justify-content-between"><span>Belanja Bahan (Mutasi Masuk)</span><b><?= rupiah((float)($belanjaBahan['total'] ?? 0)) ?> <small>(<?= (int)($belanjaBahan['jml'] ?? 0) ?>x)</small></b></div>
    </div></div>
  </div>
  <div class="col-md-4"><div class="card h-100"><div class="card-header">Per Status</div>
    <div class="card-body py-2">
      <?php if ($perStatus === []): ?><div class="text-muted">Tidak ada data.</div>
      <?php else: foreach ($perStatus as $s): ?>
        <div class="d-flex justify-content-between"><span><?= status_badge($s['status']) ?></span><b><?= $s['jml'] ?></b></div>
      <?php endforeach; endif; ?>
    </div></div>
  </div>
  <div class="col-md-4"><div class="card h-100"><div class="card-header">Ringkasan</div>
    <div class="card-body py-2">
      <div class="d-flex justify-content-between mb-1"><span>Pesanan Selesai</span><b><?= (int)($summary['selesai'] ?? 0) ?></b></div>
      <div class="d-flex justify-content-between mb-1"><span>Periode</span><b><?= e($dari) ?> s.d. <?= e($sampai) ?></b></div>
    </div></div>
  </div>
</div>

<div class="card"><div class="card-header">Penjualan per Produk (Omzet / HPP / Laba)</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Produk</th><th>Kategori</th><th>Qty</th><th>Omzet</th><th>HPP</th><th>Laba</th><th>Margin</th></tr></thead>
      <tbody>
      <?php foreach ($perProduk as $p):
          $omz = (float)$p['omzet']; $hppP = (float)$p['hpp']; $lb = $omz - $hppP;
          $mg = ($omz > 0) ? ($lb / $omz * 100.0) : 0.0; ?>
        <tr><td><?= e($p['nama']) ?></td><td><?= e($p['kategori']) ?></td>
          <td><?= $p['qty'] ?></td><td><?= rupiah($omz) ?></td>
          <td><?= rupiah($hppP) ?></td>
          <td class="<?= $lb >= 0 ? 'text-success' : 'text-danger' ?>"><?= rupiah($lb) ?></td>
          <td><?= number_format($mg, 1, ',', '.') ?>%</td></tr>
      <?php endforeach; ?>
      <?php if ($perProduk === []): ?><tr><td colspan="7" class="text-muted">Tidak ada data periode ini.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div></div>