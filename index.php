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