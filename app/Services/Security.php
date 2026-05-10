<?php
namespace App\Services;

class Security {
    public static function clean(?string $value): string {
        return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
