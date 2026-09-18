<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Utilisateurs</div>
        <div style="font-size:22px; font-weight:bold;">531</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Comptes désactivés</div>
        <div style="font-size:22px; font-weight:bold;">3</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Rôles configurés</div>
        <div style="font-size:22px; font-weight:bold;">6</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Actions aujourd'hui</div>
        <div style="font-size:22px; font-weight:bold;">27</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : administrateur).
        Le contenu réel de cette page sera développé ultérieurement.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
