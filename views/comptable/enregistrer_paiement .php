<?php
// À placer dans : views/comptable/enregistrer_paiement.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';

// À VÉRIFIER : la clé exacte de $_SESSION['user']['role'] selon config/session.php
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'comptable') {
    header('Location: ' . BASE_URL . 'views/auth/login.php');
    exit;
}

$controller = new PaiementController();
$message = "";
$erreur = "";
$inscription = null;

if (isset($_GET['idInscription'])) {
    $inscriptions = $controller->getInscriptionsValidees();
    foreach ($inscriptions as $ins) {
        if ($ins['idInscription'] == $_GET['idInscription']) {
            $inscription = $ins;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = $_POST['montant'];
    $modePaiement = $_POST['modePaiement'];
    $reference = $_POST['reference'];
    $idInscription = $_POST['idInscription'];

    if ($controller->enregistrerPaiement($montant, $modePaiement, $reference, $idInscription)) {
        $message = "Paiement enregistré avec succès.";
        // On recharge l'inscription pour rafraîchir le total payé affiché
        $inscriptions = $controller->getInscriptionsValidees();
        foreach ($inscriptions as $ins) {
            if ($ins['idInscription'] == $idInscription) {
                $inscription = $ins;
                break;
            }
        }
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

$pageTitle = 'Enregistrer un paiement';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h1 style="margin-bottom:16px;">Enregistrer un paiement</h1>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<?php if ($inscription): ?>
    <div class="card" style="background:var(--primary-light); margin-bottom:20px;">
        <h3 style="margin-top:0;">Informations de l'étudiant</h3>
        <p><strong>Étudiant :</strong> <?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></p>
        <p><strong>Matricule :</strong> <?php echo htmlspecialchars($inscription['matricule']); ?></p>
        <p><strong>Niveau :</strong> <?php echo htmlspecialchars($inscription['niveau_libelle']); ?></p>
        <p><strong>Année scolaire :</strong> <?php echo htmlspecialchars($inscription['anneeScolaire']); ?></p>
        <p><strong>Total déjà payé :</strong> <?php echo number_format($totalPaye, 2, ',', ' '); ?> FCFA</p>
    </div>
<?php endif; ?>

<div class="card" style="max-width:480px;">
    <form method="POST">
        <?php if ($inscription): ?>
            <input type="hidden" name="idInscription" value="<?php echo $inscription['idInscription']; ?>">
        <?php else: ?>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;" for="idInscription">Inscription :</label>
                <select id="idInscription" name="idInscription" required style="width:100%; padding:8px;">
                    <option value="">Sélectionner une inscription</option>
                    <?php
                    $inscriptions = $controller->getInscriptionsValidees();
                    foreach ($inscriptions as $ins):
                    ?>
                        <option value="<?php echo $ins['idInscription']; ?>">
                            <?php echo htmlspecialchars($ins['nom'] . ' ' . $ins['prenom'] . ' - ' . $ins['niveau_libelle']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="montant">Montant (FCFA) :</label>
            <input type="number" id="montant" name="montant" step="0.01" required style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="modePaiement">Mode de paiement :</label>
            <select id="modePaiement" name="modePaiement" required style="width:100%; padding:8px;">
                <option value="">Sélectionner</option>
                <option value="ESPECES">Espèces</option>
                <option value="CHEQUE">Chèque</option>
                <option value="VIREMENT">Virement</option>
                <option value="CARTE">Carte bancaire</option>
                <option value="MOBILE_MONEY">Mobile Money</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="reference">Référence :</label>
            <input type="text" id="reference" name="reference" placeholder="Ex: PAY-2026-0001" style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <button type="submit" class="btn" style="width:100%;">Enregistrer le paiement</button>
    </form>
</div>

<?php if (count($paiements) > 0): ?>
    <div class="card" style="margin-top:24px; overflow-x:auto;">
        <h3 style="margin-top:0;">Historique des paiements</h3>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:var(--primary); color:#fff;">
                    <th style="padding:10px; text-align:left;">Date</th>
                    <th style="padding:10px; text-align:left;">Montant</th>
                    <th style="padding:10px; text-align:left;">Mode</th>
                    <th style="padding:10px; text-align:left;">Référence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;"><?php echo htmlspecialchars($paiement['datePaiement']); ?></td>
                        <td style="padding:10px;"><?php echo number_format($paiement['montant'], 2, ',', ' '); ?> FCFA</td>
                        <td style="padding:10px;"><?php echo htmlspecialchars($paiement['modePaiement']); ?></td>
                        <td style="padding:10px;"><?php echo htmlspecialchars($paiement['referencePaiement'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<p style="margin-top:20px;"><a href="paiements_etudiants.php" class="btn">Retour à la liste</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
