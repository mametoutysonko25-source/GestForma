<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['comptable']);
$statement = database()->prepare(
    'SELECT i.idInscription, i.dateInscription, d.anneeScolaire,
            e.matricule, u.nom, u.prenom, n.libelle AS niveauLibelle,
            COALESCE(SUM(p.montant), 0) AS totalPaye
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN etudiant e ON e.idUtilisateur = d.idEtudiant
     INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     LEFT JOIN paiement p ON p.idInscription = i.idInscription
    WHERE i.statut = :statut
     GROUP BY i.idInscription, i.dateInscription, d.anneeScolaire,
              e.matricule, u.nom, u.prenom, n.libelle
     HAVING totalPaye = 0
    ORDER BY i.dateInscription DESC'
);
$statement->execute(['statut' => 'VALIDEE']);
$impayes = $statement->fetchAll();

$pageTitle = 'Impayés';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Inscriptions impayées</h2>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Année scolaire</th>
                <th style="padding:10px; text-align:left;">Total payé</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($impayes as $impaye): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($impaye['prenom'] . ' ' . $impaye['nom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($impaye['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($impaye['niveauLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($impaye['anneeScolaire']) ?></td>
                    <td style="padding:10px; color:#c62828; font-weight:bold;">0,00 FCFA</td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$impayes): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucun impayé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
