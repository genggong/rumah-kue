<h4>📋 Kelola Pesanan</h4>
<form method="get" class="row g-2 mb-3">
  <div class="col-auto"><select class="form-select" name="status" onchange="this.form.submit()">
    <option value="">Semua status</option>
    <?php foreach (['baru','verifikasi','produksi','selesai','batal'] as $s): ?>
      <option value="<?= $s ?>" <?= $filter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
    <?php endforeach; ?>
  </select></div>
</form>

<?php if ($pesanan === []): ?><div class="alert alert-info">Tidak ada pesanan.</div>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover bg-white rounded shadow-sm">
  <thead><tr><th>No</th><th>Pemesan</th><th>Tanggal</th><th>Jam</th><th>Item</th><th>Total</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($pesanan as $p): ?>
    <tr>
      <td class="fw-bold"><?= e($p['no_pesanan']) ?></td>
      <td><?= e($p['nama_pemesan']) ?></td>
      <td><?= format_hari($p['tanggal']) ?></td>
      <td class="fw-bold"><?= e($p['jam']) ?></td>
      <td><?= $p['jml_item'] ?></td>
      <td><?= rupiah((float)$p['total']) ?></td>
      <td><?= status_badge($p['status']) ?></td>
      <td><a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/kelola/detail?id=<?= $p['id'] ?>">Detail</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>