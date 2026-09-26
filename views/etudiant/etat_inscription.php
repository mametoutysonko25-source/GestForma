<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

$currentUser = requireRole(['etudiant']);
$db = database();

$inscription = $db->prepare(
    'SELECT i.idInscription, i.dateInscription, i.statut, n.libelle AS niveauLibelle,
            d.anneeScolaire
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     WHERE d.idEtudiant = :idEtudiant
     ORDER BY i.idInscription DESC
     LIMIT 1'
);
$inscription->execute(['idEtudiant' => $currentUser['id']]);
$inscription = $inscription->fetch();

$pageTitle = 'État de l’inscription';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">État de votre inscription</h2>

<?php if ($inscription): ?>
    <div class="card" style="max-width:560px;">
        <p><strong>Année scolaire :</strong> <?= htmlspecialchars($inscription['anneeScolaire']) ?></p>
        <p><strong>Niveau :</strong> <?= htmlspecialchars($inscription['niveauLibelle']) ?></p>
        <p><strong>Date de demande :</strong> <?= htmlspecialchars($inscription['dateInscription']) ?></p>
        <p><strong>Statut :</strong>
            <span style="font-weight:bold; color: <?= $inscription['statut'] === 'VALIDEE' ? '#2e7d32' : ($inscription['statut'] === 'REFUSEE' ? '#c62828' : '#e65100') ?>;">
                <?= htmlspecialchars($inscription['statut']) ?>
            </span>
        </p>
    </div>
<?php else: ?>
    <div class="card" style="max-width:560px; color:var(--muted);">
        <p>Aucune demande d’inscription trouvée.</p>
    </div>
<?php endif; ?>

<p style="margin-top:20px;"><a href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/demande_inscription.php') ?>" class="btn">Nouvelle demande</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
