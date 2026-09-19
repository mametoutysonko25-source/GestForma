<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$roles = [
	'etudiant' => 'ETUDIANT',
	'responsable' => 'RESPONSABLE_PEDAGOGIQUE',
	'comptable' => 'COMPTABLE',
	'formateur' => 'FORMATEUR',
	'administrateur' => 'ADMINISTRATEUR',
	'directeur' => 'DIRECTEUR',
];

if (($_GET['action'] ?? '') === 'logout') {
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path']);
	}
	session_destroy();
	header('Location: /index.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: /index.php');
	exit;
}

$email = trim($_POST['email'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$role = $_POST['role'] ?? '';

if ($email === '' || $password === '' || !isset($roles[$role])) {
	header('Location: /index.php?erreur=champs');
	exit;
}

$statement = database()->prepare(
	'SELECT u.* FROM UTILISATEUR u
	 INNER JOIN ' . $roles[$role] . ' r ON r.idUtilisateur = u.idUtilisateur
	 WHERE LOWER(u.email) = LOWER(:email) AND u.statutCompte = :statut
	 LIMIT 1'
);
$statement->execute(['email' => $email, 'statut' => 'ACTIF']);
$user = $statement->fetch();

$passwordMatches = $user && (
	password_verify($password, $user['motDePasseHash'])
	|| hash_equals((string) $user['motDePasseHash'], $password)
);

if (!$passwordMatches) {
	header('Location: /index.php?erreur=identifiants');
	exit;
}

session_regenerate_id(true);
$_SESSION['idUtilisateur'] = (int) $user['idUtilisateur'];
$_SESSION['role'] = $role;
$_SESSION['nomUtilisateur'] = trim($user['prenom'] . ' ' . $user['nom']);

$destinations = [
	'etudiant' => '/views/etudiant/demande_inscription.php',
	'responsable' => '/views/responsable/valider_inscription.php',
	'comptable' => '/views/comptable/paiements_etudiants.php',
];

header('Location: ' . ($destinations[$role] ?? '/index.php'));
exit;
