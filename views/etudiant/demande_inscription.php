<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

$currentUser = requireRole(['etudiant']);

$controller = new InscriptionController();
$message = "";
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNiveau = filter_input(INPUT_POST, 'idNiveau', FILTER_VALIDATE_INT);
    $anneeScolaire = trim($_POST['anneeScolaire'] ?? '');
    $idEtudiant = $currentUser['id'];

    if (!$idNiveau || !preg_match('/^\d{4}-\d{4}$/', $anneeScolaire)) {
        $erreur = "Veuillez sélectionner un niveau et une année scolaire valides.";
    } elseif ($controller->demanderInscription($idEtudiant, $idNiveau, $anneeScolaire)) {
        $message = "Votre demande d'inscription a été soumise avec succès.";
    } else {
        $erreur = "Une demande est déjà en attente ou validée pour ce dossier.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'inscription</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .message { color: green; margin-bottom: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Demande d'inscription</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($erreur): ?>
        <div class="erreur"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="anneeScolaire">Année scolaire :</label>
            <input type="text" id="anneeScolaire" name="anneeScolaire" value="2026-2027" required>
        </div>

        <div class="form-group">
            <label for="idNiveau">Niveau souhaité :</label>
            <select id="idNiveau" name="idNiveau" required>
                <option value="">Sélectionner un niveau</option>
                <option value="1">L1 - Première année</option>
                <option value="2">L2 - Deuxième année</option>
                <option value="3">L3 - Troisième année</option>
            </select>
        </div>

        <button type="submit">Soumettre la demande</button>
    </form>

    <p><a href="etat_inscription.php">Voir l'état de ma demande</a></p>
    <p><a href="../../index.php?logout=1">Se déconnecter / changer de rôle</a></p>
</body>
</html>