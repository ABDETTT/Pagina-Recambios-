<?php
function initSecureSession(): void {
    if (session_status() !== PHP_SESSION_NONE) return;
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', '7200');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
    if (!isset($_SESSION['_sr']) || (time() - $_SESSION['_sr']) > 1800) {
        session_regenerate_id(true);
        $_SESSION['_sr'] = time();
    }
}

function csrfToken(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrfField(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES) . '">';
}

function csrfCheck(): bool {
    $token = $_POST['_csrf'] ?? '';
    return !empty($token)
        && !empty($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

function rateLimit(string $key, int $max = 5, int $window = 300): bool {
    $now = time();
    $entries = $_SESSION['_rl'][$key] ?? [];
    $entries = array_values(array_filter($entries, fn($t) => ($now - $t) < $window));
    if (count($entries) >= $max) return false;
    $entries[] = $now;
    $_SESSION['_rl'][$key] = $entries;
    return true;
}
