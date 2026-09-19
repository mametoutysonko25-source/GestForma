<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['responsable']);

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
        <div style="font-size:12px; color:var(--muted);">Demandes en attente</div>
        <div style="font-size:22px; font-weight:bold;">9</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Formations actives</div>
        <div style="font-size:22px; font-weight:bold;">14</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Taux de présence</div>
        <div style="font-size:22px; font-weight:bold;">91%</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : responsable pédagogique).
        Le contenu réel de cette page sera développé ultérieurement.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
