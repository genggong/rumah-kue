<h4>🥚 Bahan Baku & Stok</h4>
<button class="btn btn-primary mb-3" style="background:#8d5524;border:none" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Bahan Baru</button>

<?php if ($bahan === []): ?><div class="alert alert-info">Belum ada bahan baku. Tambahkan dulu.</div>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover bg-white rounded shadow-sm">
  <thead><tr><th>Bahan</th><th>Satuan</th><th>Stok</th><th>Min</th><th>Harga/Satuan</th><th class="text-end">Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($bahan as $b): $menipis = (float)$b['stok'] <= (float)$b['min_stok']; ?>
    <tr class="<?= $menipis ? 'table-warning' : '' ?>">
      <td class="fw-bold"><?= e($b['nama']) ?> <?= $menipis ? '<span class="badge bg-danger">MENIPIS</span>' : '' ?></td>
      <td><?= e($b['satuan']) ?></td>
      <td class="fw-bold"><?= $b['stok'] ?> <?= e($b['satuan']) ?></td>
      <td><?= $b['min_stok'] ?></td>
      <td><?= rupiah((float)$b['harga_satuan']) ?></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalMutasi<?= $b['id'] ?>">Stok +/−</button>
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $b['id'] ?>">Edit</button>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="<?= BASE_URL ?>/bahan/tambah"><?= csrf_field() ?>
    <div class="modal-header"><h5 class="modal-title">Tambah Bahan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Nama</label><input class="form-control" name="nama" required></div>
      <div class="row">
        <div class="col-6 mb-2"><label class="form-label">Satuan</label><input class="form-control" name="satuan" value="pcs"></div>
        <div class="col-6 mb-2"><label class="form-label">Harga/Satuan (Rp)</label><input class="form-control" name="harga_satuan" type="number" min="0" value="0"></div>
      </div>
      <div class="row"><div class="col-6"><label class="form-label">Stok Awal</label><input class="form-control" name="stok" type="number" min="0" value="0"></div>
      <div class="col-6"><label class="form-label">Min. Stok</label><input class="form-control" name="min_stok" type="number" min="0" value="5"></div></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary" style="background:#8d5524;border:none">Simpan</button></div>
  </form>
</div></div></div>

<?php foreach ($bahan as $b): ?>
<!-- Modal Mutasi -->
<div class="modal fade" id="modalMutasi<?= $b['id'] ?>"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="<?= BASE_URL ?>/bahan/stok"><?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $b['id'] ?>">
    <div class="modal-header"><h5 class="modal-title">Mutasi Stok — <?= e($b['nama']) ?> (stok <?= $b['stok'] ?>)</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Jenis</label>
        <select class="form-select" name="jenis"><option value="masuk">Masuk (stok bertambah)</option><option value="keluar">Keluar (stok berkurang)</option><option value="koreksi">Koreksi (set langsung)</option></select></div>
      <div class="mb-2"><label class="form-label">Jumlah</label><input class="form-control" type="number" name="jumlah" min="0" step="any" required></div>
      <div><label class="form-label">Catatan</label><input class="form-control" name="catatan" placeholder="Pembelian baru, produksi, dll"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary" style="background:#8d5524;border:none">Simpan</button></div>
  </form>
</div></div></div>

<!-- Modal Ubah -->
<div class="modal fade" id="modalUbah<?= $b['id'] ?>"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="<?= BASE_URL ?>/bahan/ubah"><?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $b['id'] ?>">
    <div class="modal-header"><h5 class="modal-title">Edit — <?= e($b['nama']) ?></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Nama</label><input class="form-control" name="nama" value="<?= e($b['nama']) ?>" required></div>
      <div class="row"><div class="col-6 mb-2"><label class="form-label">Satuan</label><input class="form-control" name="satuan" value="<?= e($b['satuan']) ?>"></div>
      <div class="col-6 mb-2"><label class="form-label">Harga/Satuan (Rp)</label><input class="form-control" name="harga_satuan" type="number" min="0" value="<?= e((string)$b['harga_satuan']) ?>"></div></div>
      <div class="mb-2"><label class="form-label">Min. Stok</label><input class="form-control" name="min_stok" type="number" min="0" value="<?= e((string)$b['min_stok']) ?>"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary" style="background:#8d5524;border:none">Simpan</button></div>
  </form>
</div></div></div>
<?php endforeach; ?>