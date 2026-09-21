<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

$currentUser = requireRole(['etudiant']);
$statement = database()->prepare(
    'SELECT i.idInscription, i.statut AS statutInscription,
            COALESCE(SUM(p.montant), 0) AS totalPaye,
            COUNT(p.idPaiement) AS nombrePaiements
     FROM etudiant e
     LEFT JOIN dossier_etudiant d ON d.idEtudiant = e.idUtilisateur
     LEFT JOIN inscription i ON i.idDossier = d.idDossier
     LEFT JOIN paiement p ON p.idInscription = i.idInscription
     WHERE e.idUtilisateur = :id
     GROUP BY i.idInscription, i.statut
     ORDER BY i.idInscription DESC
     LIMIT 1'
);
$statement->execute(['id' => $currentUser['id']]);
$finance = $statement->fetch();

$pageTitle = 'Situation financière';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Situation financière</h2>

<?php if ($finance && $finance['idInscription']): ?>
    <div class="card" style="max-width:640px;">
        <p><strong>Inscription :</strong> <?= htmlspecialchars($finance['statutInscription']) ?></p>
        <p><strong>Total payé :</strong> <?= number_format((float) $finance['totalPaye'], 2, ',', ' ') ?> FCFA</p>
        <p><strong>Nombre de paiements :</strong> <?= (int) $finance['nombrePaiements'] ?></p>
    </div>
<?php else: ?>
    <div class="card" style="max-width:640px;">
        <p style="color:var(--muted);">Aucune inscription ou aucun paiement enregistré.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
