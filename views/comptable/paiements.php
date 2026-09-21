<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';

requireRole(['comptable']);
$controller = new PaiementController();
$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $idPaiement = (int) ($_POST['idPaiement'] ?? 0);
    if ($action === 'supprimer' && $idPaiement > 0) {
        $message = $controller->supprimerPaiement($idPaiement)
            ? 'Paiement supprimé.'
            : null;
        $erreur = $message ? null : 'Le paiement n’a pas pu être supprimé.';
    }
}

$paiements = $controller->getAllPaiements();

$pageTitle = 'Paiements étudiants';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Paiements étudiants</h2>
<?php if ($message): ?><p style="color:#217a4b;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($erreur): ?><p style="color:#c62828;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
<p><a class="btn" href="<?= htmlspecialchars(BASE_URL . 'views/comptable/enregistrer_paiement.php') ?>">Ajouter un paiement</a></p>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Montant</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paiements as $paiement): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['niveau_libelle']) ?></td>
                    <td style="padding:10px;"><?= number_format((float) $paiement['montant'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="<?= htmlspecialchars(BASE_URL . 'views/comptable/enregistrer_paiement.php?idPaiement=' . (int) $paiement['idPaiement']) ?>">Détail / modifier</a>
                        <form method="post" style="display:inline; margin-left:8px;" onsubmit="return confirm('Supprimer ce paiement ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idPaiement" value="<?= (int) $paiement['idPaiement'] ?>">
                            <button type="submit" style="border:0; background:none; color:#c62828; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$paiements): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucun paiement enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>