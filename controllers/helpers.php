<?php

require_once __DIR__ . '/../config/session.php';

function requireRole(array $rolesAutorises): array
{
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
        header('Location: /views/auth/login.php?erreur=connexion_requise');
        exit;
    }

    $_SESSION['nom_utilisateur'] = $_SESSION['user']['nom'];
    $_SESSION['role_utilisateur'] = $_SESSION['user']['role'];
    return $_SESSION['user'];
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}
