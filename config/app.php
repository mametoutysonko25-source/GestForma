<?php

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = (strpos($scriptName, '/GestForma/') === 0) ? '/GestForma/' : '/';
define('BASE_URL', $basePath);

define('DASHBOARD_PAR_ROLE', [
    'etudiant'       => BASE_URL . 'views/etudiant/dashboard.php',
    'formateur'      => BASE_URL . 'views/formateur/dashboard.php',
    'responsable'    => BASE_URL . 'views/responsable/dashboard.php',
    'comptable'      => BASE_URL . 'views/comptable/dashboard.php',
    'directeur'      => BASE_URL . 'views/directeur/dashboard.php',
    'administrateur' => BASE_URL . 'views/administrateur/dashboard.php',
]);
