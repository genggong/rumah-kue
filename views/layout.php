<?php $u = $_user ?? null; $flash = $_flash ?? []; $active = $_active ?? ''; $title = $_title ?? 'Rumah Kue'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> — Rumah Kue</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
:root { --rk-brown: #8d5524; --rk-cream: #fdf6ec; }
body { background: var(--rk-cream); font-family: 'Segoe UI', system-ui, sans-serif; }
.navbar-rk { background: linear-gradient(135deg, #8d5524, #c68642); }
.navbar-rk .navbar-brand { color:#fff; font-weight:700; }
.navbar-rk .nav-link { color: rgba(255,255,255,.9); }
.navbar-rk .nav-link.active { font-weight: 700; color: #fff; border-bottom: 2px solid #fff; }
.card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.07); border-radius: 12px; }
.card-header { background: #fff; border-bottom: 1px solid #f0e6d8; font-weight: 600; }
.stat-card { border-radius: 12px; padding: 18px; color: #fff; }
.stat-card .num { font-size: 2rem; font-weight: 700; }
.badge { font-weight: 600; }
.footer-rk { text-align:center; padding: 18px; color:#8d5524; font-size:.85rem; }
.table th { background: #f8f0e4; }
.table td { vertical-align: middle; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-rk mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/">🍰 Rumah Kue</a>
    <?php if ($u !== null): ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php if ($u['role'] === 'pemesan'): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='pesan'?'active':'' ?>" href="<?= BASE_URL ?>/pesan">Buat Pesanan</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='riwayat'?'active':'' ?>" href="<?= BASE_URL ?>/pesanan/saya">Riwayat Saya</a></li>
        <?php endif; ?>
        <?php if (in_array($u['role'], ['admin','produksi'], true)): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='kelola'||$active==='produksi'?'active':'' ?>" href="<?= BASE_URL ?>/kelola">Pesanan</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='bahan'?'active':'' ?>" href="<?= BASE_URL ?>/bahan">Bahan Baku</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='produk'?'active':'' ?>" href="<?= BASE_URL ?>/produk">Produk & Resep</a></li>
        <?php endif; ?>
        <?php if ($u['role'] === 'produksi'): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='produksi'?'active':'' ?>" href="<?= BASE_URL ?>/produksi">Antrian Produksi</a></li>
        <?php endif; ?>
        <?php if ($u['role'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='pengguna'?'active':'' ?>" href="<?= BASE_URL ?>/pengguna">Pengguna</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='laporan'?'active':'' ?>" href="<?= BASE_URL ?>/laporan">Laporan</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">👤 <?= e($u['nama']) ?> (<?= role_label($u['role']) ?>)</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/">Dashboard</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><form method="post" action="<?= BASE_URL ?>/logout"><?= csrf_field() ?><button class="dropdown-item text-danger" type="submit">Keluar</button></form></li>
          </ul>
        </li>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</nav>

<?php foreach ($flash as $f): ?>
  <div class="container"><div class="alert alert-<?= e($f['type'] === 'error' ? 'danger' : 'success') ?> alert-dismissible fade show shadow-sm">
    <?= e($f['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div></div>
<?php endforeach; ?>

<main class="container pb-4"><?php require $viewFile; ?></main>

<footer class="footer-rk">© <?= date('Y') ?> Rumah Kue — Manajemen Pesanan, Bahan Baku & Produksi</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>