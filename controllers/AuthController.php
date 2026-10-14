<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('login');
    }

    public function login(): void
    {
        csrf_verify();
        $username = $this->post('username');
        $password = (string)($_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            Flash::set('error', 'Username dan password wajib diisi.');
            redirect('/login');
        }
        if (!Auth::attempt($username, $password)) {
            Flash::set('error', 'Username atau password salah.');
            redirect('/login');
        }
        Flash::set('success', 'Selamat datang, ' . e(Auth::user()['nama']) . '!');
        redirect('/');
    }

    public function logout(): void
    {
        csrf_verify();
        Auth::logout();
        redirect('/login');
    }
}