<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT u.*, GROUP_CONCAT(r.name SEPARATOR ', ') as roles
             FROM users u
             LEFT JOIN user_roles ur ON ur.user_id = u.id
             LEFT JOIN roles r ON r.id = ur.role_id
             GROUP BY u.id
             ORDER BY u.name ASC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email, string $password, ?string $nip = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, nip) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            $nip
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, ?string $password = null, ?string $nip = null): bool
    {
        if ($password) {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = ?, email = ?, password = ?, nip = ? WHERE id = ?"
            );
            return $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                $nip,
                $id
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = ?, email = ?, nip = ? WHERE id = ?"
            );
            return $stmt->execute([$name, $email, $nip, $id]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Role Management
    public function getAllRoles(): array
    {
        return $this->db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
    }

    public function getRoles(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.name FROM roles r
             JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function getByRole(string $roleName): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.* FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r ON r.id = ur.role_id
             WHERE r.name = ?
             ORDER BY u.name ASC"
        );
        $stmt->execute([$roleName]);
        return $stmt->fetchAll();
    }

    public function assignRole(int $userId, int $roleId): bool
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $roleId]);
    }

    public function removeAllRoles(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    // Notifications
    public function createNotification(int $userId, string $message): bool
    {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        return $stmt->execute([$userId, $message]);
    }

    public function getNotifications(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function countUnreadNotifications(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$notificationId]);
    }

    public function markAllNotificationsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
