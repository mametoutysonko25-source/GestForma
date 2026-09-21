<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
$currentUser = requireRole(['comptable']);
$db = database();
$paiementsMois = (float) $db->query("SELECT COALESCE(SUM(montant), 0) FROM paiement WHERE datePaiement >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')")->fetchColumn();
$aPayer = (int) $db->query('SELECT COUNT(*) FROM remuneration_formateur WHERE montantDu > montantPaye')->fetchColumn();
$impayes = (int) $db->query("SELECT COUNT(*) FROM (SELECT i.idInscription FROM inscription i LEFT JOIN paiement p ON p.idInscription = i.idInscription WHERE i.statut = 'VALIDEE' GROUP BY i.idInscription HAVING COALESCE(SUM(p.montant), 0) = 0) AS impayes")->fetchColumn();

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Paiements du mois</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format($paiementsMois, 0, ',', ' ') ?> F</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Impayés</div>
        <div style="font-size:22px; font-weight:bold;"><?= $impayes ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Formateurs à payer</div>
        <div style="font-size:22px; font-weight:bold;"><?= $aPayer ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Solde global</div>
        <div style="font-size:22px; font-weight:bold;">-</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : comptable).
        Les indicateurs sont calculés à partir des paiements et rémunérations enregistrés.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
