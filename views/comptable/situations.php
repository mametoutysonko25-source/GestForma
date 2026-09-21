<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['comptable']);
$statement = database()->query(
    'SELECT i.idInscription, i.statut AS statutInscription,
            d.anneeScolaire, e.matricule, u.nom, u.prenom,
            n.libelle AS niveauLibelle,
            COALESCE(SUM(p.montant), 0) AS totalPaye
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN etudiant e ON e.idUtilisateur = d.idEtudiant
     INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     LEFT JOIN paiement p ON p.idInscription = i.idInscription
     GROUP BY i.idInscription, i.statut, d.anneeScolaire,
              e.matricule, u.nom, u.prenom, n.libelle
     ORDER BY i.dateInscription DESC'
);
$situations = $statement->fetchAll();

$pageTitle = 'Situations financières';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Situations financières</h2>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Statut</th>
                <th style="padding:10px; text-align:left;">Total payé</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($situations as $situation): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($situation['prenom'] . ' ' . $situation['nom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($situation['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($situation['niveauLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($situation['statutInscription']) ?></td>
                    <td style="padding:10px; font-weight:bold;"><?= number_format((float) $situation['totalPaye'], 2, ',', ' ') ?> FCFA</td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$situations): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucune situation financière.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
