<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['comptable']);
$statement = database()->query(
    'SELECT r.mois, r.heuresValidees, r.montantDu, r.montantPaye,
            r.statut, f.matricule, u.nom, u.prenom
     FROM remuneration_formateur r
     INNER JOIN formateur f ON f.idUtilisateur = r.idFormateur
     INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
     WHERE r.montantDu > r.montantPaye
     ORDER BY r.mois DESC, u.nom, u.prenom'
);
$remunerations = $statement->fetchAll();

$pageTitle = 'Rémunérations';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Rémunérations à payer</h2>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Formateur</th>
                <th style="padding:10px; text-align:left;">Mois</th>
                <th style="padding:10px; text-align:left;">Heures</th>
                <th style="padding:10px; text-align:left;">Montant dû</th>
                <th style="padding:10px; text-align:left;">Montant payé</th>
                <th style="padding:10px; text-align:left;">Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($remunerations as $remuneration): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['prenom'] . ' ' . $remuneration['nom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['mois']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['heuresValidees']) ?></td>
                    <td style="padding:10px;"><?= number_format((float) $remuneration['montantDu'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px;"><?= number_format((float) $remuneration['montantPaye'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px; color:#c62828; font-weight:bold;"><?= htmlspecialchars($remuneration['statut']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$remunerations): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune rémunération à payer.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
