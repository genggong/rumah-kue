<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function conn(): PDO
    {
        if (self::$pdo === null) {
            $dir = BASE_PATH . '/db';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $file = $dir . '/rumahkue.db';
            self::$pdo = new PDO('sqlite:' . $file, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$pdo->exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON;');
            self::migrate(self::$pdo);
        }
        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    username    TEXT NOT NULL UNIQUE,
    password    TEXT NOT NULL,
    role        TEXT NOT NULL CHECK (role IN ('admin','produksi','pemesan')),
    nama        TEXT NOT NULL,
    hp          TEXT DEFAULT '',
    aktif       INTEGER NOT NULL DEFAULT 1,
    created_at  TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS bahan_baku (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nama         TEXT NOT NULL UNIQUE,
    satuan       TEXT NOT NULL DEFAULT 'pcs',
    stok         REAL NOT NULL DEFAULT 0,
    harga_satuan REAL NOT NULL DEFAULT 0,
    min_stok     REAL NOT NULL DEFAULT 0,
    updated_at   TEXT
);

CREATE TABLE IF NOT EXISTS produk (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    nama        TEXT NOT NULL,
    kategori    TEXT NOT NULL CHECK (kategori IN ('KUE','NASI')),
    harga_jual  REAL NOT NULL DEFAULT 0,
    aktif       INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS produk_bahan (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    produk_id  INTEGER NOT NULL,
    bahan_id   INTEGER NOT NULL,
    qty        REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE,
    FOREIGN KEY (bahan_id)  REFERENCES bahan_baku(id)
);

CREATE TABLE IF NOT EXISTS pesanan (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    no_pesanan  TEXT NOT NULL UNIQUE,
    pemesan_id  INTEGER NOT NULL,
    tanggal     TEXT NOT NULL,
    jam         TEXT NOT NULL,
    status      TEXT NOT NULL DEFAULT 'baru'
                CHECK (status IN ('baru','verifikasi','produksi','selesai','batal')),
    catatan     TEXT DEFAULT '',
    total       REAL NOT NULL DEFAULT 0,
    created_at  TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (pemesan_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS pesanan_item (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    pesanan_id INTEGER NOT NULL,
    produk_id  INTEGER NOT NULL,
    qty        REAL NOT NULL DEFAULT 1,
    harga      REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id)  REFERENCES produk(id)
);

CREATE TABLE IF NOT EXISTS timeline (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    pesanan_id INTEGER NOT NULL,
    tahap      TEXT NOT NULL,
    catatan    TEXT DEFAULT '',
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS stok_mutasi (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    bahan_id   INTEGER NOT NULL,
    pesanan_id INTEGER,
    jenis      TEXT NOT NULL CHECK (jenis IN ('masuk','keluar','koreksi')),
    jumlah     REAL NOT NULL DEFAULT 0,
    catatan    TEXT DEFAULT '',
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (bahan_id)   REFERENCES bahan_baku(id),
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id)
);

CREATE INDEX IF NOT EXISTS idx_pesanan_status ON pesanan(status);
CREATE INDEX IF NOT EXISTS idx_pesanan_tanggal ON pesanan(tanggal);
CREATE INDEX IF NOT EXISTS idx_timeline_pesanan ON timeline(pesanan_id);
SQL;
        $pdo->exec($sql);

        // Seed: admin default
        $st = $pdo->query('SELECT COUNT(*) c FROM users WHERE role="admin"');
        if ((int)$st->fetch()['c'] === 0) {
            $pdo->prepare('INSERT INTO users (username, password, role, nama) VALUES (?,?,?,?)')
                ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin', 'Administrator']);
        }
        // Seed: user produksi
        $st = $pdo->query('SELECT COUNT(*) c FROM users WHERE role="produksi"');
        if ((int)$st->fetch()['c'] === 0) {
            $pdo->prepare('INSERT INTO users (username, password, role, nama) VALUES (?,?,?,?)')
                ->execute(['produksi', password_hash('produksi123', PASSWORD_DEFAULT), 'produksi', 'Tim Produksi']);
        }
        // Seed: user pemesan (contoh)
        $st = $pdo->query('SELECT COUNT(*) c FROM users WHERE role="pemesan"');
        if ((int)$st->fetch()['c'] === 0) {
            $pdo->prepare('INSERT INTO users (username, password, role, nama, hp) VALUES (?,?,?,?,?)')
                ->execute(['pemesan', password_hash('pemesan123', PASSWORD_DEFAULT), 'pemesan', 'Pemesan Contoh', '081234567890']);
        }
    }

    public static function all(string $sql, array $params = []): array
    {
        $st = self::conn()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $st = self::conn()->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public static function run(string $sql, array $params = []): int
    {
        $pdo = self::conn();
        $st  = $pdo->prepare($sql);
        $st->execute($params);
        return (int)$pdo->lastInsertId();
    }
}