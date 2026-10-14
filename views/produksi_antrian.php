<h4>🏭 Antrian Produksi <span class="text-muted fs-6">(urut sesuai hari & jam pengambilan)</span></h4>
<p class="text-muted">Timeline pengerjaan: pesanan dengan tanggal & jam paling awal dikerjakan lebih dulu.</p>

<?php if ($timeline === []): ?><div class="alert alert-info">Tidak ada pesanan dalam antrian produksi.</div>
<?php else: ?>
<div class="card mb-4"><div class="card-header">⏰ Timeline Pengerjaan (hari & jam)</div>
  <div class="card-body p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>Urutan</th><th>No Pesanan</th><th>Hari</th><th>Jam</th><th>Pemesan</th><th>Item</th></tr></thead>
      <tbody>
      <?php foreach ($timeline as $i => $t): ?>
        <tr><td class="fw-bold">#<?= $i + 1 ?></td><td><?= e($t['no']) ?></td>
          <td><?= format_hari($t['tanggal']) ?></td><td class="fw-bold"><?= e($t['jam']) ?></td>
          <td><?= e($t['pemesan']) ?></td><td><?= $t['items'] ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div></div>
<?php endif; ?>

<div class="card"><div class="card-header">Pesanan Dalam Produksi</div>
  <div class="card-body p-0">
    <?php if ($antrian === []): ?><div class="p-3 text-muted">Antrian kosong.</div>
    <?php else: ?>
    <div class="table-responsive"><table class="table table-hover mb-0">
      <thead><tr><th>No</th><th>Pemesan</th><th>Tanggal</th><th>Jam</th><th>Item</th><th>Total</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($antrian as $a): ?>
        <tr>
          <td class="fw-bold"><?= e($a['no_pesanan']) ?></td>
          <td><?= e($a['nama_pemesan']) ?></td>
          <td><?= format_hari($a['tanggal']) ?></td>
          <td class="fw-bold"><?= e($a['jam']) ?></td>
          <td>
            <?php foreach ($a['items'] as $it): ?>
              <div class="small">• <?= e($it['nama']) ?> × <?= $it['qty'] ?></div>
            <?php endforeach; ?>
          </td>
          <td><?= rupiah((float)$a['total']) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/kelola/detail?id=<?= $a['id'] ?>">Detail</a>
            <form method="post" action="<?= BASE_URL ?>/produksi/selesai" class="d-inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>">
              <button class="btn btn-sm btn-success">✓ Selesai</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </div></div>