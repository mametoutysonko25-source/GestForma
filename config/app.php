<?php

if (!defined('BASE_URL')) {
    define('BASE_URL', '/GestForma/');
}

if (!defined('DASHBOARD_PAR_ROLE')) {
    define('DASHBOARD_PAR_ROLE', [
        'etudiant'       => BASE_URL . 'views/etudiant/dashboard.php',
        'formateur'      => BASE_URL . 'views/formateur/dashboard.php',
        'responsable'    => BASE_URL . 'views/responsable/dashboard.php',
        'comptable'      => BASE_URL . 'views/comptable/dashboard.php',
        'directeur'      => BASE_URL . 'views/directeur/dashboard.php',
        'administrateur' => BASE_URL . 'views/administrateur/dashboard.php',
    ]);
}
