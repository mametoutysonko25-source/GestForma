<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';

requireRole(['comptable']);
$inscriptions = (new PaiementController())->getInscriptionsValidees();

$pageTitle = 'Paiements étudiants';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Paiements étudiants</h2>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Total payé</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $inscription): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($inscription['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($inscription['niveau_libelle']) ?></td>
                    <td style="padding:10px;"><?= number_format((float) $inscription['total_paye'], 2, ',', ' ') ?> FCFA</td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$inscriptions): ?>
                <tr><td colspan="4" style="padding:16px; text-align:center; color:var(--muted);">Aucune inscription validée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>