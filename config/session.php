<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,        // expire à la fermeture du navigateur
        'path'     => '/',
        'httponly' => true,     // inaccessible en JS (protection XSS basique)
        'samesite' => 'Lax',
    ]);
    // NB : on garde le nom de session PAR DÉFAUT de PHP (PHPSESSID)
    // volontairement, plutôt qu'un nom personnalisé — les includes du
    // Groupe 3 (navbar.php, sidebar.php, header.php) appellent un simple
    // session_start() sans configuration particulière ; un nom personnalisé
    // ici créerait une session différente de la leur et casserait la
    // connexion. Si un nom personnalisé est vraiment souhaité, il doit
    // être fixé AUSSI dans les fichiers du Groupe 3, sinon la session ne
    // sera jamais partagée entre les deux parties.
    session_start();
}

/**
 * Régénère l'identifiant de session (à appeler après un login réussi,
 * pour se prémunir des attaques de fixation de session).
 */
function regenerateSession(): void
{
    session_regenerate_id(true);
}
