<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';
require_once __DIR__ . '/../../config/app.php';

requireRole(['comptable']);
$controller = new PaiementController();
$idPaiement = (int) ($_GET['idPaiement'] ?? $_POST['idPaiement'] ?? 0);
$paiement = $idPaiement > 0 ? $controller->getPaiement($idPaiement) : null;
$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = (float) ($_POST['montant'] ?? 0);
    $modePaiement = trim($_POST['modePaiement'] ?? '');
    $reference = trim($_POST['reference'] ?? '');
    $idInscription = (int) ($_POST['idInscription'] ?? 0);

    if ($montant <= 0 || $modePaiement === '') {
        $erreur = 'Le montant et le mode de paiement sont obligatoires.';
    } elseif ($idPaiement > 0) {
        $ok = $controller->modifierPaiement($idPaiement, $montant, $modePaiement, $reference);
        $message = $ok ? 'Paiement modifié.' : null;
        $erreur = $ok ? null : 'La modification a échoué.';
        $paiement = $controller->getPaiement($idPaiement);
    } elseif ($idInscription <= 0) {
        $erreur = 'Sélectionnez une inscription.';
    } else {
        $ok = $controller->enregistrerPaiement($montant, $modePaiement, $reference, $idInscription);
        $message = $ok ? 'Paiement enregistré.' : null;
        $erreur = $ok ? null : 'L’enregistrement a échoué.';
    }
}

$inscriptions = $controller->getInscriptionsValidees();
$pageTitle = $paiement ? 'Détail du paiement' : 'Ajouter un paiement';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;"><?= htmlspecialchars($pageTitle) ?></h2>
<?php if ($message): ?><p style="color:#217a4b;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($erreur): ?><p style="color:#c62828;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

<div class="card" style="max-width:640px;">
    <?php if ($paiement): ?>
        <p><strong>Étudiant :</strong> <?= htmlspecialchars($paiement['prenom'] . ' ' . $paiement['nom']) ?></p>
        <p><strong>Inscription :</strong> <?= (int) $paiement['idInscription'] ?> · <?= htmlspecialchars($paiement['niveau_libelle']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($paiement['datePaiement']) ?></p>
    <?php endif; ?>

    <form method="post">
        <?php if ($paiement): ?>
            <input type="hidden" name="idPaiement" value="<?= (int) $paiement['idPaiement'] ?>">
        <?php else: ?>
            <div class="form-field">
                <label for="idInscription">Inscription validée</label>
                <select id="idInscription" name="idInscription" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($inscriptions as $inscription): ?>
                        <option value="<?= (int) $inscription['idInscription'] ?>">
                            <?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom'] . ' - ' . $inscription['niveau_libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-field">
            <label for="montant">Montant (FCFA)</label>
            <input type="number" id="montant" name="montant" min="0.01" step="0.01" required value="<?= htmlspecialchars($paiement['montant'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label for="modePaiement">Mode de paiement</label>
            <select id="modePaiement" name="modePaiement" required>
                <?php foreach (['ESPECES' => 'Espèces', 'CHEQUE' => 'Chèque', 'VIREMENT' => 'Virement', 'CARTE' => 'Carte bancaire', 'MOBILE_MONEY' => 'Mobile Money'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($paiement['modePaiement'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="reference">Référence</label>
            <input type="text" id="reference" name="reference" maxlength="100" value="<?= htmlspecialchars($paiement['reference'] ?? '') ?>">
        </div>
        <button class="btn" type="submit"><?= $paiement ? 'Enregistrer les modifications' : 'Enregistrer le paiement' ?></button>
        <a href="<?= htmlspecialchars(BASE_URL . 'views/comptable/paiements.php') ?>" style="margin-left:12px;">Retour à la liste</a>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>