<?php

class SettingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Get setting value by key
     */
    public function get(string $key, $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE `key` = ? LIMIT 1");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    }

    /**
     * Update or insert setting
     */
    public function set(string $key, string $value): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
        );
        return $stmt->execute([$key, $value]);
    }

    /**
     * Get all settings as associative array
     */
    public function getAll(): array
    {
        $results = $this->db->query("SELECT `key`, `value` FROM settings")->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
