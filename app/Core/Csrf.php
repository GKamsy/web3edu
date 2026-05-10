<?php
declare(strict_types=1);

namespace App\Core;

class Csrf {
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function validate(?string $token): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!$token || empty($_SESSION['_csrf'])) {
            return false;
        }

        return hash_equals($_SESSION['_csrf'], $token);
    }

    public static function regenerate(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    // Destroy CSRF token safely
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['_csrf']);
    }
}
