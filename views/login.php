<div class="row justify-content-center mt-5">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header text-center">🍰 <strong>Masuk — Rumah Kue</strong></div>
      <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/login">
          <?= csrf_field() ?>
          <div class="mb-3"><label class="form-label">Username</label>
            <input class="form-control" name="username" required autofocus></div>
          <div class="mb-3"><label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required></div>
          <button class="btn btn-primary w-100" style="background:#8d5524;border:none">Masuk</button>
        </form>
        <div class="mt-3 small text-muted">
          Akun default: <code>admin / admin123</code> • <code>produksi / produksi123</code>
        </div>
      </div>
    </div>
  </div>
</div>