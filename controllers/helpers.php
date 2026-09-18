<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';

function requireRole(array $rolesAutorises): array
{
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
<<<<<<< HEAD
        header('Location: /views/auth/login.php?erreur=connexion_requise');
=======
        header('Location: ' . BASE_URL . 'views/auth/login.php');
>>>>>>> 817486c1d7c15f130a8ca5759f13afe0a570c390
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
