<?php

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): void
    {
        Middleware::requireGuest();

        if (!isPost()) {
            renderView('auth/login', [
                'title' => 'Login – ' . APP_NAME,
            ]);
            return;
        }

        // Validasi CSRF
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token keamanan tidak valid. Silakan coba lagi.');
            redirect('?page=login');
        }

        $email = sanitize(post('email'));
        $password = post('password');

        // Validasi input
        $errors = [];
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }
        if (empty($password)) {
            $errors[] = 'Password wajib diisi.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old_email', $email);
            redirect('?page=login');
        }

        // Cek user
        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            flashError('Email atau password salah.');
            Session::flash('old_email', $email);
            redirect('?page=login');
        }

        // Login berhasil – regenerate session ID (security)
        Session::regenerate();

        // Load roles
        $roles = $this->userModel->getRoles((int) $user['id']);

        // Set session
        Session::set('user_id', (int) $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_roles', $roles);

        flashSuccess('Selamat datang, ' . e($user['name']) . '!');
        redirect('?page=dashboard');
    }

    public function logout(): void
    {
        // Simpan pesan sebelum destroy
        $successMsg = 'Anda telah berhasil logout.';
        Session::destroy();
        // Mulai session baru untuk membawa flash message
        Session::start();
        Session::regenerate();
        flashSuccess($successMsg);
        redirect('?page=login');
    }
}
