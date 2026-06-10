<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /filmbase/login.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!isLoggedIn()) {
        header('Location: /filmbase/login.php');
        exit;
    }
    if (!isAdmin()) {
        header('Location: /filmbase/index.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'        => $_SESSION['user_id']    ?? null,
        'firstname' => $_SESSION['firstname']  ?? '',
        'lastname'  => $_SESSION['lastname']   ?? '',
        'email'     => $_SESSION['email']      ?? '',
        'role'      => $_SESSION['role']       ?? 'user',
    ];
}

function loginUser(array $user): void {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname']  = $user['lastname'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role']      = $user['role'];
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
