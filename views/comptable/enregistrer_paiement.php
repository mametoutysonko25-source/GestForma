<?php
session_start();
require_once __DIR__ . '/../../controllers/PaiementController.php';

if (!isset($_SESSION['idUtilisateur'])) {
    header("Location: ../../index.php");
    exit();
}

$controller = new PaiementController();
if (!$controller->estComptable($_SESSION['idUtilisateur'])) {
    http_response_code(403);
    exit('Accès réservé au comptable.');
}
$message = "";
$erreur = "";
$inscription = null;

<<<<<<< Updated upstream
if (isset($_GET['idInscription'])) {
    $inscriptions = $controller->getInscriptionsValidees();
    foreach ($inscriptions as $ins) {
        if ($ins['idInscription'] == $_GET['idInscription']) {
            $inscription = $ins;
            break;
        }
    }
=======
$idInscription = filter_input(INPUT_GET, 'idInscription', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idInscription = filter_input(INPUT_POST, 'idInscription', FILTER_VALIDATE_INT);
}
if ($idInscription) {
    $inscription = $controller->getInscriptionValideeById($idInscription);
>>>>>>> Stashed changes
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
    $modePaiement = trim($_POST['modePaiement'] ?? '');
    $reference = trim($_POST['reference'] ?? '');

    if (!$inscription || $montant === false || $montant <= 0 || $modePaiement === '') {
        $erreur = "Veuillez sélectionner une inscription validée et saisir un montant positif.";
    } elseif ($controller->enregistrerPaiement($montant, $modePaiement, $reference ?: null, $idInscription)) {
        $message = "Paiement enregistré avec succès.";
        $inscription = $controller->getInscriptionValideeById($idInscription);
    } else {
        $erreur = "Une erreur est survenue lors de l'enregistrement du paiement.";
    }
}

$paiements = [];
$totalPaye = 0;
if ($inscription) {
    $paiements = $controller->getPaiementsByInscription($inscription['idInscription']);
    $totalPaye = $controller->getTotalPaye($inscription['idInscription']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrer un paiement</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .message { color: green; margin-bottom: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Enregistrer un paiement</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="erreur"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <?php if ($inscription): ?>
        <div class="info">
            <h3>Informations de l'étudiant</h3>
            <p><strong>Étudiant :</strong> <?php echo $inscription['nom'] . ' ' . $inscription['prenom']; ?></p>
            <p><strong>Matricule :</strong> <?php echo $inscription['matricule']; ?></p>
            <p><strong>Niveau :</strong> <?php echo $inscription['niveau_libelle']; ?></p>
            <p><strong>Année scolaire :</strong> <?php echo $inscription['anneeScolaire']; ?></p>
            <p><strong>Total déjà payé :</strong> <?php echo number_format($totalPaye, 2, ',', ' '); ?> FCFA</p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php if ($inscription): ?>
            <input type="hidden" name="idInscription" value="<?php echo $inscription['idInscription']; ?>">
        <?php else: ?>
            <div class="form-group">
                <label for="idInscription">Inscription :</label>
                <select id="idInscription" name="idInscription" required>
                    <option value="">Sélectionner une inscription</option>
                    <?php
                    $inscriptions = $controller->getInscriptionsValidees();
                    foreach ($inscriptions as $ins): 
                    ?>
                        <option value="<?php echo $ins['idInscription']; ?>">
                            <?php echo $ins['nom'] . ' ' . $ins['prenom'] . ' - ' . $ins['niveau_libelle']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="montant">Montant (FCFA) :</label>
            <input type="number" id="montant" name="montant" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="modePaiement">Mode de paiement :</label>
            <select id="modePaiement" name="modePaiement" required>
                <option value="">Sélectionner</option>
                <option value="ESPECES">Espèces</option>
                <option value="CHEQUE">Chèque</option>
                <option value="VIREMENT">Virement</option>
                <option value="CARTE">Carte bancaire</option>
                <option value="MOBILE_MONEY">Mobile Money</option>
            </select>
        </div>

        <div class="form-group">
            <label for="reference">Référence :</label>
            <input type="text" id="reference" name="reference" placeholder="Ex: PAY-2026-0001">
        </div>

        <button type="submit">Enregistrer le paiement</button>
    </form>

    <?php if (count($paiements) > 0): ?>
        <h3>Historique des paiements</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Mode</th>
                    <th>Référence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?php echo $paiement['datePaiement']; ?></td>
                        <td><?php echo number_format($paiement['montant'], 2, ',', ' '); ?> FCFA</td>
                        <td><?php echo $paiement['modePaiement']; ?></td>
                        <td><?php echo $paiement['reference']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="paiements_etudiants.php">Retour à la liste</a></p>
<p><a href="../../index.php?logout=1">Se déconnecter / changer de rôle</a></p>
</body>
</html>