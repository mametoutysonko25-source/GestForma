<?php
require __DIR__ . '/models/Utilisateur.php';
$accounts = [
    'etudiant' => ['email' => 'etudiant@centre-formation.sn', 'password' => 'Etudiant@2025'],
    'formateur' => ['email' => 'formateur@centre-formation.sn', 'password' => 'Formateur@2025'],
    'responsable' => ['email' => 'pedagogie@centre-formation.sn', 'password' => 'Pedagogie@2025'],
    'comptable' => ['email' => 'comptable@centre-formation.sn', 'password' => 'Comptable@2025'],
    'administrateur' => ['email' => 'admin@centre-formation.sn', 'password' => 'Admin@2025'],
    'directeur' => ['email' => 'directeur@centre-formation.sn', 'password' => 'Directeur@2025'],
];
foreach ($accounts as $role => $account) {
    $user = User::findForLogin($account['email'], $role);
    $ok = $user !== null && User::verifyPassword($account['password'], $user['motDePasseHash']) && User::estActif($user);
    echo $role . ' => ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
    if (!$user) {
        continue;
    }
    echo '  user=' . $user['prenom'] . ' ' . $user['nom'] . ' / statut=' . $user['statutCompte'] . PHP_EOL;
}
