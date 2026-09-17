<?php


require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';


function requireRole(array $rolesAutorises): array
{
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
        header('Location: ' . BASE_URL . 'views/auth/login.php');
        exit;
    }
    return $_SESSION['user'];
}


function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}
