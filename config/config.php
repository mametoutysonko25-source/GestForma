<<<<<<< HEAD
=======
<?php

/**
 * BASE_URL est calculé automatiquement à partir de l'emplacement réel
 * du projet sur le disque, comparé à la racine du serveur web
 * ($_SERVER['DOCUMENT_ROOT']).
 *
 * Ça évite de coder en dur un chemin comme "/GestForma/", qui ne
 * fonctionnerait que sur une machine où le projet est cloné dans un
 * dossier nommé exactement "GestForma" directement sous htdocs.
 * Chaque personne de l'équipe peut avoir cloné le dépôt ailleurs
 * (ex. htdocs/GestForma, htdocs/projet-scolarite, ou même à la
 * racine de htdocs) : ce calcul s'adapte tout seul dans tous les cas.
 */
$racineProjet = realpath(__DIR__ . '/..');
$racineServeur = realpath($_SERVER['DOCUMENT_ROOT'] ?? $racineProjet);

$sousChemin = str_replace('\\', '/', substr($racineProjet, strlen($racineServeur)));
$sousChemin = trim($sousChemin, '/');

define('BASE_URL', $sousChemin === '' ? '/' : '/' . $sousChemin . '/');

define('DASHBOARD_PAR_ROLE', [
    'etudiant'       => 'views/etudiant/dashboard.php',
    'formateur'      => 'views/formateur/dashboard.php',
    'responsable'    => 'views/responsable/dashboard.php',
    'comptable'      => 'views/comptable/dashboard.php',
    'directeur'      => 'views/directeur/dashboard.php',
    'administrateur' => 'views/administrateur/dashboard.php',
]);
>>>>>>> 817486c1d7c15f130a8ca5759f13afe0a570c390
