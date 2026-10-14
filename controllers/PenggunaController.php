<?php
declare(strict_types=1);

final class PenggunaController extends Controller
{
    public function index(): void
    {
        $users = Database::all('SELECT id, username, role, nama, hp, aktif FROM users ORDER BY role, nama');
        $this->view('pengguna_index', [
            'users' => $users,
            'title' => 'Pengguna',
            '_active' => 'pengguna',
        ]);
    }

    public function tambah(): void
    {
        csrf_verify();
        $username = $this->post('username');
        $password = (string)($_POST['password'] ?? '');
        $role     = $this->post('role', 'pemesan');
        $nama     = $this->post('nama');
        $hp       = $this->post('hp');

        if ($username === '' || $password === '' || $nama === '' || !in_array($role, ['admin', 'produksi', 'pemesan'], true)) {
            Flash::set('error', 'Username, password, nama, dan role wajib diisi valid.');
            redirect('/pengguna');
        }
        try {
            Database::run('INSERT INTO users (username, password, role, nama, hp) VALUES (?,?,?,?,?)',
                [$username, password_hash($password, PASSWORD_DEFAULT), $role, $nama, $hp]);
        } catch (\PDOException $e) {
            Flash::set('error', 'Username sudah dipakai.');
            redirect('/pengguna');
        }
        Flash::set('success', 'Pengguna "' . e($nama) . '" (' . role_label($role) . ') ditambahkan.');
        redirect('/pengguna');
    }
}