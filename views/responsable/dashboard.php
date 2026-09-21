<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
$currentUser = requireRole(['responsable']);

$nbEtudiants = (int) database()->query(
    "SELECT COUNT(*) FROM etudiant"
)->fetchColumn();

$nbDemandesEnAttente = (int) database()->query(
    "SELECT COUNT(*) FROM inscription WHERE statut = 'EN_ATTENTE'"
)->fetchColumn();

$nbFormations = (int) database()->query(
    "SELECT COUNT(*) FROM formation"
)->fetchColumn();

$presenceStats = database()->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN statutPresence = 'PRESENT' THEN 1 ELSE 0 END) AS presents
     FROM presence"
)->fetch();
$tauxPresence = ($presenceStats && $presenceStats['total'] > 0)
    ? round(($presenceStats['presents'] / $presenceStats['total']) * 100)
    : 0;

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Étudiants</div>
        <div style="font-size:22px; font-weight:bold;"><?= $nbEtudiants ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Demandes en attente</div>
        <div style="font-size:22px; font-weight:bold;"><?= $nbDemandesEnAttente ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Formations</div>
        <div style="font-size:22px; font-weight:bold;"><?= $nbFormations ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Taux de présence</div>
        <div style="font-size:22px; font-weight:bold;"><?= $tauxPresence ?>%</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : responsable pédagogique).
    </p>
    <?php if ($nbDemandesEnAttente > 0): ?>
        <p style="font-size:13px;">
            <a href="<?= htmlspecialchars(BASE_URL . 'views/responsable/inscriptions.php') ?>" class="btn">
                Traiter les <?= $nbDemandesEnAttente ?> demande<?= $nbDemandesEnAttente > 1 ? 's' : '' ?> en attente
            </a>
        </p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
