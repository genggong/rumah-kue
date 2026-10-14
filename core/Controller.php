<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        $data['_user']    = Auth::user();
        $data['_flash']   = Flash::pull();
        $data['_title']   = $data['_title'] ?? 'Rumah Kue';
        $data['_active']  = $data['_active'] ?? '';

        extract($data, EXTR_SKIP);
        $layoutFile = BASE_PATH . '/views/layout.php';
        $viewFile   = BASE_PATH . '/views/' . $template . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('View tidak ditemukan: ' . $template);
        }
        require $layoutFile;
    }

    protected function post(string $key, string $default = ''): string
    {
        $v = $_POST[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    protected function postFloat(string $key, float $default = 0.0): float
    {
        $v = $_POST[$key] ?? '';
        $v = is_string($v) ? trim(str_replace(['.', ' '], ['', ''], $v)) : '';
        $v = str_replace(',', '.', $v);
        return is_numeric($v) ? (float)$v : $default;
    }
}