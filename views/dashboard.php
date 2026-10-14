<div class="row g-3">
  <div class="col-md-3"><div class="stat-card bg-primary"><div class="num"><?= $jumlah_pesanan ?></div><div>Total Pesanan</div></div></div>
  <div class="col-md-3"><div class="stat-card bg-info"><div class="num"><?= $pesanan_baru ?></div><div>Pesanan Baru</div></div></div>
  <div class="col-md-3"><div class="stat-card" style="background:#8d5524"><div class="num"><?= $pesanan_produksi ?></div><div>Dalam Produksi</div></div></div>
  <div class="col-md-3"><div class="stat-card bg-success"><div class="num"><?= $pesanan_hari_ini ?></div><div>Pesanan Hari Ini</div></div></div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-6">
    <div class="card"><div class="card-header">⚠️ Bahan Baku Menipis (stok ≤ min)</div>
      <div class="card-body p-0">
        <?php if ($bahan_menipis === []): ?><div class="p-3 text-muted">Semua stok aman 👍</div>
        <?php else: ?>
        <table class="table table-sm mb-0"><thead><tr><th>Bahan</th><th>Stok</th><th>Min</th></tr></thead><tbody>
        <?php foreach ($bahan_menipis as $b): ?>
          <tr><td><?= e($b['nama']) ?></td><td class="text-danger fw-bold"><?= $b['stok'] ?> <?= e($b['satuan']) ?></td><td><?= $b['min_stok'] ?></td></tr>
        <?php endforeach; ?>
        </tbody></table><?php endif; ?>
      </div></div>
  </div>
  <div class="col-md-6">
    <div class="card"><div class="card-header">📋 Antrian Produksi (urut hari & jam)</div>
      <div class="card-body p-0">
        <?php if ($antrian_produksi === []): ?><div class="p-3 text-muted">Tidak ada pesanan dalam produksi.</div>
        <?php else: ?>
        <table class="table table-sm mb-0"><thead><tr><th>No Pesanan</th><th>Tanggal</th><th>Jam</th></tr></thead><tbody>
        <?php foreach ($antrian_produksi as $a): ?>
          <tr><td><?= e($a['no_pesanan']) ?></td><td><?= format_hari($a['tanggal']) ?></td><td class="fw-bold"><?= e($a['jam']) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table><?php endif; ?>
      </div></div>
  </div>
</div>