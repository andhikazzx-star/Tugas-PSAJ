<?php

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman daftar pengguna
     */
    public function index(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        renderView('admin/users', [
            'title' => 'Manajemen Pengguna – ' . APP_NAME,
            'users' => $this->userModel->getAll(),
            'roles' => $this->userModel->getAllRoles(),
        ]);
    }

    /**
     * Simpan pengguna baru
     */
    public function create(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.users');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.users');
        }

        $name = sanitize(post('name'));
        $email = sanitize(post('email'));
        $password = post('password');
        $roleIds = post('role_ids', []);
        $nip = sanitize(post('nip'));

        if (empty($name) || empty($email) || empty($password)) {
            flashError('Semua field wajib diisi.');
            redirect('?page=admin.users');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashError('Email tidak valid.');
            redirect('?page=admin.users');
        }
        if (strlen($password) < 6) {
            flashError('Password minimal 6 karakter.');
            redirect('?page=admin.users');
        }

        if ($this->userModel->findByEmail($email)) {
            flashError('Email sudah terdaftar.');
            redirect('?page=admin.users');
        }

        $userId = $this->userModel->create($name, $email, $password, $nip);
        if (is_array($roleIds)) {
            foreach ($roleIds as $roleId) {
                $this->userModel->assignRole($userId, (int) $roleId);
            }
        }

        flashSuccess("Pengguna {$name} berhasil ditambahkan.");
        redirect('?page=admin.users');
    }

    /**
     * Update data pengguna
     */
    public function update(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.users');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.users');
        }

        $id = (int) post('user_id');
        $name = sanitize(post('name'));
        $email = sanitize(post('email'));
        $password = post('password');
        $roleIds = post('role_ids', []);
        $nip = sanitize(post('nip'));

        if (empty($name) || empty($email)) {
            flashError('Nama dan email wajib diisi.');
            redirect('?page=admin.users');
        }

        $this->userModel->update($id, $name, $email, $password ?: null, $nip);
        $this->userModel->removeAllRoles($id);
        if (is_array($roleIds)) {
            foreach ($roleIds as $roleId) {
                $this->userModel->assignRole($id, (int) $roleId);
            }
        }

        flashSuccess('Data pengguna berhasil diperbarui.');
        redirect('?page=admin.users');
    }

    /**
     * Hapus pengguna
     */
    public function delete(): void
    {
        Middleware::requireRole([ROLE_ADMIN]);
        if (!isPost())
            redirect('?page=admin.users');
        if (!validateCsrfToken(post('_csrf_token'))) {
            flashError('Token tidak valid.');
            redirect('?page=admin.users');
        }

        $id = (int) post('user_id');
        if ($id === Session::getUserId()) {
            flashError('Tidak bisa menghapus akun sendiri.');
            redirect('?page=admin.users');
        }

        $this->userModel->delete($id);
        flashSuccess('Pengguna berhasil dihapus.');
        redirect('?page=admin.users');
    }
}
