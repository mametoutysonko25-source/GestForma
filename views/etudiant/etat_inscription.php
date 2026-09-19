<?php
session_start();
require_once '../../controllers/InscriptionController.php';

if (!isset($_SESSION['idUtilisateur'])) {
    header("Location: ../../index.php");
    exit();
}

$controller = new InscriptionController();
$inscription = $controller->getEtatInscription($_SESSION['idUtilisateur']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État de l'inscription</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; }
        .statut { font-weight: bold; }
        .en-attente { color: orange; }
        .validee { color: green; }
        .refusee { color: red; }
    </style>
</head>
<body>
    <h1>État de votre inscription</h1>

    <?php if ($inscription): ?>
        <div class="info">
            <p><strong>Niveau :</strong> <?php echo $inscription['idNiveau']; ?></p>
            <p><strong>Date de demande :</strong> <?php echo $inscription['dateInscription']; ?></p>
            <p><strong>Statut :</strong> 
                <span class="statut 
                    <?php 
                    if ($inscription['statut'] === 'EN_ATTENTE') echo 'en-attente';
                    elseif ($inscription['statut'] === 'VALIDEE') echo 'validee';
                    else echo 'refusee';
                    ?>">
                    <?php echo $inscription['statut']; ?>
                </span>
            </p>
        </div>
    <?php else: ?>
        <p>Aucune demande d'inscription trouvée.</p>
    <?php endif; ?>

    <p><a href="demande_inscription.php">Nouvelle demande</a></p>
</body>
</html>