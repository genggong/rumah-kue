<?php
declare(strict_types=1);

final class Flash
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function pull(): array
    {
        self::start();
        $items = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $items;
    }
}

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Sesi berakhir atau token tidak valid.');
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function old(string $key, string $default = ''): string
{
    return (string)($_POST[$key] ?? $default);
}

function rupiah(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

function format_hari(string $tgl): string
{
    $ts = strtotime($tgl);
    if ($ts === false) {
        return $tgl;
    }
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $hari[(int)date('w', $ts)] . ', ' . date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function status_badge(string $status): string
{
    return match ($status) {
        'baru'       => '<span class="badge bg-info">Baru</span>',
        'verifikasi' => '<span class="badge bg-warning text-dark">Verifikasi</span>',
        'produksi'   => '<span class="badge bg-primary">Produksi</span>',
        'selesai'    => '<span class="badge bg-success">Selesai</span>',
        'batal'      => '<span class="badge bg-secondary">Batal</span>',
        default      => $status,
    };
}

function role_label(string $role): string
{
    return match ($role) {
        'admin'    => 'Admin',
        'produksi' => 'Produksi',
        'pemesan'  => 'Pemesan',
        default    => $role,
    };
}