<?php
session_start();

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path']);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

$destinations = [
    'etudiant' => 'views/etudiant/demande_inscription.php',
    'responsable' => 'views/responsable/valider_inscription.php',
    'comptable' => 'views/comptable/paiements_etudiants.php',
];

if (isset($_SESSION['idUtilisateur'], $_SESSION['role'], $destinations[$_SESSION['role']])) {
    header('Location: ' . $destinations[$_SESSION['role']]);
    exit;
}

$message = $_GET['erreur'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de Formation - Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 520px; margin: 50px auto; padding: 20px; }
        form { display: grid; gap: 12px; }
        input, select, button { padding: 10px; font-size: 15px; }
        button { background: #007bff; color: white; border: 0; cursor: pointer; }
        .error { color: #b00020; }
    </style>
</head>
<body>
    <h1>Centre de Formation</h1>
    <p>Connectez-vous pour accéder à votre espace.</p>
    <?php if ($message): ?><p class="error">E-mail, mot de passe ou rôle incorrect.</p><?php endif; ?>
    <form method="post" action="controllers/AuthController.php?action=login">
        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email" required>
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required>
        <label for="role">Rôle</label>
        <select id="role" name="role" required>
            <option value="">Sélectionner un rôle</option>
            <option value="etudiant">Étudiant</option>
            <option value="responsable">Responsable pédagogique</option>
            <option value="comptable">Comptable</option>
            <option value="formateur">Formateur</option>
            <option value="administrateur">Administrateur</option>
            <option value="directeur">Directeur</option>
        </select>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
<?php
session_start();

// Simulation de connexion (à remplacer par votre système d'authentification)
if (!isset($_SESSION['idUtilisateur'])) {
    // Pour les tests, définir manuellement un utilisateur
    // $_SESSION['idUtilisateur'] = 5; // Étudiant
    // $_SESSION['idUtilisateur'] = 2; // Responsable pédagogique
    // $_SESSION['idUtilisateur'] = 3; // Comptable
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Centre de Formation - Connexion</title>
    </head>
    <body>
        <h1>Centre de Formation</h1>
        <p>Veuillez vous connecter pour accéder à l'application.</p>
        <p><em>Pour les tests, définissez $_SESSION['idUtilisateur'] dans ce fichier.</em></p>
    </body>
    </html>
    <?php
    exit();
}

// Redirection selon le rôle
header("Location: views/etudiant/demande_inscription.php");
?>