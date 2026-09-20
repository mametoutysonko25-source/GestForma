<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/EspaceEtudiantController.php';
$currentUser = requireRole(['etudiant']);
$espace = new EspaceEtudiantController();
$modules = $espace->modules((int) $currentUser['id']);
$planning = $espace->planning((int) $currentUser['id']);
$resultats = $espace->resultats((int) $currentUser['id']);
$finance = $espace->finance((int) $currentUser['id']);
$totalPaye = array_sum(array_map(static fn (array $ligne): float => (float) $ligne['totalPaye'], $finance));

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard"; // doit correspondre à une clé du menu dans includes/sidebar.php
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;"><?= count($modules) ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Présences</div>
        <div style="font-size:22px; font-weight:bold;"><?= count($planning) ? 'Disponible' : 'N/A' ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Moyenne</div>
        <div style="font-size:22px; font-weight:bold;"><?= count($resultats) ? number_format(array_sum(array_map(static fn (array $ligne): float => (float) $ligne['note'], $resultats)) / count($resultats), 2, ',', ' ') : 'N/A' ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Solde</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format($totalPaye, 0, ',', ' ') ?> F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">Les indicateurs sont calculés à partir de votre dossier, de vos séances, de vos résultats et de vos paiements.</p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
