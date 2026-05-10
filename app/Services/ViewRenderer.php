<?php
namespace App\Services;

class ViewRenderer
{
    public static function render(string $path, array $data = []): string {
        extract($data);
        ob_start();
        require $path;
        return ob_get_clean();
    }
}
