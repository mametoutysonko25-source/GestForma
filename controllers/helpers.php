<?php

require_once __DIR__ . '/../config/session.php';

/**
 * Vérifie que l'utilisateur connecté a l'un des rôles autorisés.
 * Redirige vers la connexion sinon. Retourne les infos de l'utilisateur.
 *
 * Exemple d'utilisation en tête d'une vue :
 *   require_once __DIR__ . '/../../controllers/helpers.php';
 *   $currentUser = requireRole(['etudiant']);
 */
function requireRole(array $rolesAutorises): array
{
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
        header('Location: /views/auth/login.php');
        exit;
    }
    return $_SESSION['user'];
}

/**
 * Retourne l'utilisateur connecté, ou null si personne n'est connecté.
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}
