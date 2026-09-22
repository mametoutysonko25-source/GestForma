<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
if (!defined('BASE_URL')) {
    define('BASE_URL', '/GestForma/');
}

function requireRole(array $rolesAutorises): array
{
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $rolesAutorises, true)) {
        header('Location: ' . BASE_URL . 'views/auth/login.php?erreur=connexion_requise');
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

function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function consumeFlash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function journaliserAction(string $action, ?string $details = null, ?int $utilisateurId = null): void
{
    $utilisateurId = $utilisateurId ?? (int) ($_SESSION['user']['id'] ?? 0);
    if ($utilisateurId <= 0) {
        return;
    }

    try {
        $requete = database()->prepare(
            'INSERT INTO historique (utilisateurId, action, details) VALUES (:utilisateurId, :action, :details)'
        );
        $requete->execute([
            'utilisateurId' => $utilisateurId,
            'action' => $action,
            'details' => $details,
        ]);
    } catch (PDOException $exception) {
        // Le journal ne doit pas empêcher une opération métier de réussir.
    }
}
