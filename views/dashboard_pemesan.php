<h4>Halo, <?= e($_user['nama']) ?> 👋</h4>
<p class="text-muted">Pesanan terbaru Anda:</p>
<?php if ($pesanan === []): ?>
  <div class="alert alert-info">Belum ada pesanan. <a href="<?= BASE_URL ?>/pesan">Buat pesanan sekarang</a></div>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover bg-white rounded shadow-sm">
  <thead><tr><th>No</th><th>Tanggal</th><th>Jam</th><th>Total</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($pesanan as $p): ?>
    <tr>
      <td><?= e($p['no_pesanan']) ?></td>
      <td><?= format_hari($p['tanggal']) ?></td>
      <td class="fw-bold"><?= e($p['jam']) ?></td>
      <td><?= rupiah((float)$p['total']) ?></td>
      <td><?= status_badge($p['status']) ?></td>
      <td><a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/pesanan/saya/detail?id=<?= $p['id'] ?>">Detail</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>