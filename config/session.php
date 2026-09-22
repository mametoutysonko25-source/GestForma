<?php

// Le Sénégal utilise UTC toute l'année, sans changement saisonnier.
date_default_timezone_set('Africa/Dakar');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,        // expire à la fermeture du navigateur
        'path'     => '/',
        'httponly' => true,     // inaccessible en JS (protection XSS basique)
        'samesite' => 'Lax',
    ]);
    
    session_start();
}


function regenerateSession(): void
{
    session_regenerate_id(true);
}
