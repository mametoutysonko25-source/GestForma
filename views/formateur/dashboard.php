<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['formateur']);

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;">4</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Séances cette semaine</div>
        <div style="font-size:22px; font-weight:bold;">6</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Copies à corriger</div>
        <div style="font-size:22px; font-weight:bold;">12</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Rémunération (mois)</div>
        <div style="font-size:22px; font-weight:bold;">150 000 F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : formateur).
        Le contenu réel de cette page sera développé ultérieurement.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
