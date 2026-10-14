<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">📜 Resep — <?= e($produk['nama']) ?> <span class="badge bg-<?= $produk['kategori']==='KUE'?'danger':'primary' ?>"><?= e($produk['kategori']) ?></span></h4>
  <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/produk">← Kembali</a>
</div>

<div class="row g-3">
  <div class="col-md-7">
    <div class="card"><div class="card-header">Daftar Bahan & Kebutuhan per Produk</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>Bahan</th><th>Satuan</th><th>Qty</th><th>Stok Tersedia</th></tr></thead>
          <tbody>
          <?php foreach ($resep as $r): ?>
            <tr><td><?= e($r['nama_bahan']) ?></td><td><?= e($r['satuan']) ?></td>
              <td class="fw-bold"><?= $r['qty'] ?></td><td><?= $r['satuan'] ?></td></tr>
          <?php endforeach; ?>
          <?php if ($resep === []): ?><tr><td colspan="4" class="text-muted">Resep kosong — tambahkan bahan di bawah.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-body border-top">
        <strong>Estimasi biaya bahan per produk: <span class="text-success"><?= rupiah($biayaBahan) ?></span></strong><br>
        <small class="text-muted">Harga jual: <?= rupiah((float)$produk['harga_jual']) ?> → margin <?= rupiah(max(0.0, (float)$produk['harga_jual'] - $biayaBahan)) ?></small>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card"><div class="card-header">Tambah/Perbarui Resep</div>
      <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/produk/resep/simpan"><?= csrf_field() ?>
          <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
          <div id="resepItems">
            <div class="row g-2 mb-2 resep-row">
              <div class="col-7"><select class="form-select" name="bahan[]"><option value="">— pilih bahan —</option>
                <?php foreach ($bahan as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['nama']) ?> (<?= e($b['satuan']) ?>)</option><?php endforeach; ?>
              </select></div>
              <div class="col-3"><input class="form-control" type="number" name="qty[]" min="0" step="any" value="1"></div>
              <div class="col-2"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.resep-row').remove()">✕</button></div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="tambahResep()">+ Tambah baris</button>
          <button class="btn btn-primary w-100" style="background:#8d5524;border:none">Simpan Resep</button>
        </form>
      </div></div>
  </div>
</div>
<script>
function tambahResep() {
  const tag = document.getElementById('resepItems');
  const row = tag.querySelector('.resep-row').cloneNode(true);
  row.querySelector('select').value = '';
  row.querySelector('input').value = '1';
  tag.appendChild(row);
}
</script>