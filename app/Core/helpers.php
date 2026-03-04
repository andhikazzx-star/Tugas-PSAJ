<?php

/**
 * Render view dengan data.
 * Otomatis menyertakan data layout (notifications, unread) untuk user yang login.
 */
function renderView(string $view, array $data = []): void
{
    // Auto-inject layout data jika user sedang login
    if (Session::isLoggedIn() && !isset($data['notifications'])) {
        $uModel = new UserModel();
        $uid = Session::getUserId();
        $data['notifications'] = $uModel->getNotifications($uid, 5);
        $data['unread'] = $uModel->countUnreadNotifications($uid);
    }

    // Default pageTitle dari title jika belum di-set
    if (!isset($data['pageTitle']) && isset($data['title'])) {
        $data['pageTitle'] = explode(' – ', $data['title'])[0];
    }

    extract($data);
    $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        http_response_code(404);
        echo "<h1>View not found: {$view}</h1>";
    }
}

/**
 * Redirect ke URL tertentu
 */
function redirect(string $url): void
{
    header("Location: " . BASE_URL . '/' . ltrim($url, '/'));
    exit;
}

/**
 * HTML Escape
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input string
 */
function sanitize(string $value): string
{
    return trim(strip_tags($value));
}

/**
 * Validasi CSRF token
 */
function generateCsrfToken(): string
{
    if (!Session::has('csrf_token')) {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
    return Session::get('csrf_token');
}

function validateCsrfToken(string $token): bool
{
    return hash_equals(Session::get('csrf_token', ''), $token);
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(generateCsrfToken()) . '">';
}

/**
 * Flash message helpers
 */
function flashSuccess(string $msg): void
{
    Session::flash('success', $msg);
}

function flashError(string $msg): void
{
    Session::flash('error', $msg);
}

function flashWarning(string $msg): void
{
    Session::flash('warning', $msg);
}

/**
 * Format tanggal Indonesia
 */
function formatDate(string $date): string
{
    if (empty($date))
        return '-';
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $d = new DateTime($date);
    return $d->format('d') . ' ' . $bulan[(int) $d->format('m')] . ' ' . $d->format('Y');
}

/**
 * Badge status kelas
 */
function statusKelasBadge(?string $status): string
{
    $status = $status ?: 'unknown';
    $map = [
        'proses' => ['label' => 'Proses', 'class' => 'badge-warning'],
        'siap_final' => ['label' => 'Siap Final', 'class' => 'badge-info'],
        'final' => ['label' => 'Final', 'class' => 'badge-success'],
        'unknown' => ['label' => 'Tidak Diketahui', 'class' => 'badge-secondary'],
    ];
    $item = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-secondary'];
    return "<span class=\"badge {$item['class']}\">{$item['label']}</span>";
}

/**
 * Badge status nilai
 */
function statusNilaiBadge(?string $status): string
{
    $status = $status ?: 'draft';
    $map = [
        'draft' => ['label' => 'Draft', 'class' => 'badge-warning'],
        'lengkap' => ['label' => 'Lengkap', 'class' => 'badge-success'],
    ];
    $item = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-secondary'];
    return "<span class=\"badge {$item['class']}\">{$item['label']}</span>";
}

/**
 * Cek apakah request adalah POST
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Ambil POST value
 */
function post(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Ambil GET value
 */
function get_param(string $key, mixed $default = ''): mixed
{
    return $_GET[$key] ?? $default;
}

/**
 * Asset URL helper
 */
function asset(string $path): string
{
    return BASE_URL . '/public/' . ltrim($path, '/');
}

/**
 * Hitung persentase
 */
function calcPercentage(int $value, int $total): float
{
    if ($total === 0)
        return 0;
    return round(($value / $total) * 100, 1);
}
