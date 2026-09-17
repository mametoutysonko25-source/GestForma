<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';

abstract class BaseController
{
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }

    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

   
    protected function requireRole(array $rolesAutorises): array
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
            $this->redirect('/views/auth/login.php');
        }
        return $_SESSION['user'];
    }
}
