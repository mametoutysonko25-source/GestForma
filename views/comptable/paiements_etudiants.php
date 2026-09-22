<?php
// À placer dans : views/comptable/paiements_etudiants.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';

// Garde d'accès : seul le comptable peut voir cette page.
require_once __DIR__ . '/../../controllers/helpers.php';
requireRole(['comptable']);

$controller = new PaiementController();
$inscriptions = $controller->getInscriptionsValidees();

$pageTitle = 'Gestion des paiements étudiants';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h1 style="margin-bottom:6px;">Gestion des paiements étudiants</h1>
<p style="color:var(--muted); margin-top:0;">Suivi des inscriptions validées et de leur situation financière.</p>

<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Email</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Année scolaire</th>
                <th style="padding:10px; text-align:left;">Date inscription</th>
                <th style="padding:10px; text-align:left;">Total payé</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $ins): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['nom'] . ' ' . $ins['prenom']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['matricule']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['email']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['niveau_libelle']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['anneeScolaire']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($ins['dateInscription']); ?></td>
                    <td style="padding:10px; font-weight:bold; color:<?php echo $ins['total_paye'] > 0 ? '#2e7d32' : '#c62828'; ?>;">
                        <?php echo number_format($ins['total_paye'], 2, ',', ' '); ?> FCFA
                    </td>
                    <td style="padding:10px;">
                        <a href="enregistrer_paiement.php?idInscription=<?php echo $ins['idInscription']; ?>" class="btn">
                            Enregistrer paiement
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($inscriptions) === 0): ?>
                <tr><td colspan="8" style="padding:16px; text-align:center; color:var(--muted);">Aucune inscription validée pour le moment.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<p style="margin-top:20px;"><a href="enregistrer_paiement.php" class="btn">Nouveau paiement</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
