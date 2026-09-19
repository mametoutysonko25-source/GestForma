<?php
session_start();
require_once '../../controllers/InscriptionController.php';

if (!isset($_SESSION['idUtilisateur'])) {
    header("Location: ../../index.php");
    exit();
}

$controller = new InscriptionController();
$message = "";

// Traitement de la validation/refus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $idInscription = $_POST['idInscription'];
    
    if ($_POST['action'] === 'valider') {
        if ($controller->validerInscription($idInscription)) {
            $message = "Inscription validée avec succès.";
        }
    } elseif ($_POST['action'] === 'refuser') {
        if ($controller->refuserInscription($idInscription)) {
            $message = "Inscription refusée.";
        }
    }
}

$inscriptions = $controller->getInscriptionsEnAttente();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des inscriptions</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .btn { padding: 5px 10px; margin: 2px; text-decoration: none; color: white; border: none; cursor: pointer; }
        .btn-valider { background: green; }
        .btn-refuser { background: red; }
        .message { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Validation des inscriptions</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (count($inscriptions) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Matricule</th>
                    <th>Email</th>
                    <th>Niveau</th>
                    <th>Année scolaire</th>
                    <th>Date demande</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscriptions as $ins): ?>
                    <tr>
                        <td><?php echo $ins['nom'] . ' ' . $ins['prenom']; ?></td>
                        <td><?php echo $ins['matricule']; ?></td>
                        <td><?php echo $ins['email']; ?></td>
                        <td><?php echo $ins['niveau_libelle']; ?></td>
                        <td><?php echo $ins['anneeScolaire']; ?></td>
                        <td><?php echo $ins['dateInscription']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="idInscription" value="<?php echo $ins['idInscription']; ?>">
                                <button type="submit" name="action" value="valider" class="btn btn-valider">Valider</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="idInscription" value="<?php echo $ins['idInscription']; ?>">
                                <button type="submit" name="action" value="refuser" class="btn btn-refuser" 
                                    onclick="return confirm('Êtes-vous sûr de refuser cette inscription ?')">Refuser</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune inscription en attente de validation.</p>
    <?php endif; ?>
</body>
</html>