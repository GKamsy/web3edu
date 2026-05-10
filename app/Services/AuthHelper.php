<?php
namespace App\Services;

// Generate the verification code
class AuthHelper {
    public static function generateCode(int $length): string {
        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
