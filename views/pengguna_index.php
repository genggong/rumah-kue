<h4>👥 Kelola Pengguna</h4>
<button class="btn btn-primary mb-3" style="background:#8d5524;border:none" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Pengguna Baru</button>

<div class="card"><div class="card-body p-0">
  <table class="table table-hover mb-0">
    <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>HP</th><th>Aktif</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr><td><?= e($u['username']) ?></td><td><?= e($u['nama']) ?></td>
        <td><span class="badge bg-<?= $u['role']==='admin'?'danger':($u['role']==='produksi'?'primary':'secondary') ?>"><?= role_label($u['role']) ?></span></td>
        <td><?= e($u['hp']) ?></td><td><?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div></div>

<div class="modal fade" id="modalTambah"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="<?= BASE_URL ?>/pengguna/tambah"><?= csrf_field() ?>
    <div class="modal-header"><h5 class="modal-title">Tambah Pengguna</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Nama Lengkap</label><input class="form-control" name="nama" required></div>
      <div class="mb-2"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
      <div class="mb-2"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required minlength="4"></div>
      <div class="mb-2"><label class="form-label">Role</label>
        <select class="form-select" name="role"><option value="pemesan">Pemesan</option><option value="produksi">Produksi</option><option value="admin">Admin</option></select></div>
      <div><label class="form-label">No. HP</label><input class="form-control" name="hp" placeholder="08xx..."></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary" style="background:#8d5524;border:none">Simpan</button></div>
  </form>
</div></div></div>