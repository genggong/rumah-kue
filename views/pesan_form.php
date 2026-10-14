<h4>📝 Buat Pesanan Baru</h4>
<p class="text-muted">Pilih produk (kue / paket nasi), tentukan tanggal & jam pengambilan. Admin akan verifikasi & teruskan ke produksi.</p>
<form method="post" action="<?= BASE_URL ?>/pesan/simpan">
  <?= csrf_field() ?>
  <div class="row g-3 mb-3">
    <div class="col-md-4"><label class="form-label">Tanggal Pengambilan</label>
      <input class="form-control" type="date" name="tanggal" min="<?= date('Y-m-d') ?>" required></div>
    <div class="col-md-4"><label class="form-label">Jam Pengambilan</label>
      <input class="form-control" type="time" name="jam" required></div>
  </div>

  <div class="card mb-3"><div class="card-header">Item Pesanan</div>
    <div class="card-body">
      <div id="items">
        <div class="row g-2 item-row mb-2">
          <div class="col-md-7">
            <select class="form-select" name="produk[]" required>
              <option value="">— pilih produk —</option>
              <?php foreach ($produk as $pr): ?>
                <option value="<?= $pr['id'] ?>" data-kat="<?= e($pr['kategori']) ?>">
                  [<?= e($pr['kategori']) ?>] <?= e($pr['nama']) ?> — <?= rupiah((float)$pr['harga_jual']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3"><input class="form-control" type="number" name="qty[]" min="1" step="1" value="1" placeholder="Qty"></div>
          <div class="col-md-2"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.item-row').remove()">✕</button></div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="tambahItem()">+ Tambah item</button>
    </div>
  </div>

  <div class="mb-3"><label class="form-label">Catatan / Keterangan</label>
    <textarea class="form-control" name="catatan" rows="2" placeholder="Misal: tulis nama di atas kue, request topping, alamat (jika antar), dsb."></textarea></div>
  <button class="btn btn-primary px-4" style="background:#8d5524;border:none">Kirim Pesanan</button>
</form>

<script>
function tambahItem() {
  const tag = document.getElementById('items');
  const row = tag.querySelector('.item-row').cloneNode(true);
  row.querySelector('select').value = '';
  row.querySelector('input').value = '1';
  tag.appendChild(row);
}
</script>