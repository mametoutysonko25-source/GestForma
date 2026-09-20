<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';

requireRole(['comptable']);
$paiements = (new PaiementController())->getAllPaiements();

$pageTitle = 'Paiements étudiants';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Paiements étudiants</h2>
<p><a class="btn" href="paiements_etudiants.php">Gérer les paiements</a></p>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Date</th>
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Montant</th>
                <th style="padding:10px; text-align:left;">Mode</th>
                <th style="padding:10px; text-align:left;">Référence</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paiements as $paiement): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['datePaiement']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']) ?></td>
                    <td style="padding:10px;"><?= number_format((float) $paiement['montant'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px;"><?= htmlspecialchars($paiement['modePaiement']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars((string) $paiement['referencePaiement']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$paiements): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucun paiement enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>