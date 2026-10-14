<h4>🍩 Produk (Kue & Paket Nasi)</h4>
<button class="btn btn-primary mb-3" style="background:#8d5524;border:none" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Produk Baru</button>

<?php if ($produk === []): ?><div class="alert alert-info">Belum ada produk.</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($produk as $p): ?>
  <div class="col-md-4"><div class="card h-100">
    <div class="card-body">
      <span class="badge bg-<?= $p['kategori']==='KUE' ? 'danger' : 'primary' ?> mb-2"><?= e($p['kategori']) ?></span>
      <h5 class="card-title"><?= e($p['nama']) ?></h5>
      <p class="text-muted mb-1"><?= $p['jml_bahan'] ?> bahan dalam resep</p>
      <h6 class="text-success"><?= rupiah((float)$p['harga_jual']) ?></h6>
      <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/produk/resep?id=<?= $p['id'] ?>">Resep & Bahan</a>
    </div>
  </div></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="modal fade" id="modalTambah"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="<?= BASE_URL ?>/produk/tambah"><?= csrf_field() ?>
    <div class="modal-header"><h5 class="modal-title">Tambah Produk</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Nama Produk</label><input class="form-control" name="nama" required></div>
      <div class="mb-2"><label class="form-label">Kategori</label>
        <select class="form-select" name="kategori"><option value="KUE">Kue</option><option value="NASI">Paket Nasi</option></select></div>
      <div><label class="form-label">Harga Jual (Rp)</label><input class="form-control" name="harga_jual" type="number" min="1" required></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary" style="background:#8d5524;border:none">Simpan</button></div>
  </form>
</div></div></div>