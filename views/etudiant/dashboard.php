<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['etudiant']);

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard"; // doit correspondre à une clé du menu dans includes/sidebar.php
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;">6</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Présences</div>
        <div style="font-size:22px; font-weight:bold;">94%</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Moyenne</div>
        <div style="font-size:22px; font-weight:bold;">14.2</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Solde</div>
        <div style="font-size:22px; font-weight:bold;">45 000 F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Cette page est un exemple : le contenu réel (graphiques, activité récente...)
        sera branché par le Groupe 2 sur les données de la base (Groupe 1).
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
