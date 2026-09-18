<?php
session_start();

// Pour les tests, définissez manuellement l'utilisateur connecté
// Décommentez UNE SEULE des lignes suivantes (pas les trois en même temps) :

// $_SESSION['idUtilisateur'] = 5; // Étudiant
// $_SESSION['idUtilisateur'] = 2; // Responsable pédagogique
// $_SESSION['idUtilisateur'] = 3; // Comptable

// Déconnexion : ?logout=1 vide la session et revient à l'accueil
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit();
}

// Traite le clic sur un lien de test AVANT de vérifier la session
if (isset($_GET['test'])) {
    if ($_GET['test'] === 'etudiant') {
        $_SESSION['idUtilisateur'] = 5;
    } elseif ($_GET['test'] === 'responsable') {
        $_SESSION['idUtilisateur'] = 2;
    } elseif ($_GET['test'] === 'comptable') {
        $_SESSION['idUtilisateur'] = 3;
    }
}

if (!isset($_SESSION['idUtilisateur'])) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Centre de Formation - Connexion</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .test-links a { display: block; margin: 10px 0; padding: 10px; background: #007bff; color: white; text-decoration: none; }
        </style>
    </head>
    <body>
        <h1>Centre de Formation</h1>
        <p>Pour tester, cliquez sur un des liens ci-dessous :</p>
        <div class="test-links">
            <a href="?test=etudiant">Tester en tant qu'Étudiant (ID: 5)</a>
            <a href="?test=responsable">Tester en tant que Responsable (ID: 2)</a>
            <a href="?test=comptable">Tester en tant que Comptable (ID: 3)</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Redirection selon le rôle réellement en session (plus de redirection fixe vers l'étudiant)
switch ($_SESSION['idUtilisateur']) {
    case 5:
        header("Location: views/etudiant/demande_inscription.php");
        break;
    case 2:
        header("Location: views/responsable/valider_inscriptions.php");
        break;
    case 3:
        header("Location: views/comptable/paiements_etudiants.php");
        break;
    default:
        // Rôle inconnu : on efface la session invalide et on revient à l'accueil
        unset($_SESSION['idUtilisateur']);
        header("Location: index.php");
        break;
}
exit();
?>
