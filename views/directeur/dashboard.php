<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['directeur']);

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Étudiants</div>
        <div style="font-size:22px; font-weight:bold;">482</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Taux de réussite</div>
        <div style="font-size:22px; font-weight:bold;">87%</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Personnel</div>
        <div style="font-size:22px; font-weight:bold;">34</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Finances (mois)</div>
        <div style="font-size:22px; font-weight:bold;">12.4M F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : directeur).
        Le contenu réel de cette page sera développé ultérieurement.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
