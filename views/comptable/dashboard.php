<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
$currentUser = requireRole(['comptable']);
$db = database();

$stats = [
    'paiements_mois' => (float) $db->query(
        "SELECT COALESCE(SUM(montant), 0) FROM paiement WHERE MONTH(datePaiement) = MONTH(CURRENT_DATE()) AND YEAR(datePaiement) = YEAR(CURRENT_DATE())"
    )->fetchColumn(),
    'impayes' => (int) $db->query(
        "SELECT COUNT(*) FROM inscription WHERE statut = 'VALIDEE' AND idInscription NOT IN (SELECT idInscription FROM paiement GROUP BY idInscription)"
    )->fetchColumn(),
    'inscriptions_validees' => (int) $db->query(
        "SELECT COUNT(*) FROM inscription WHERE statut = 'VALIDEE'"
    )->fetchColumn(),
    'remunerations' => (int) $db->query(
        "SELECT COUNT(*) FROM remuneration_formateur WHERE montantDu > montantPaye"
    )->fetchColumn(),
];

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord comptable</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Paiements du mois</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format($stats['paiements_mois'], 0, ',', ' ') ?> FCFA</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Inscriptions impayées</div>
        <div style="font-size:22px; font-weight:bold;"><?= $stats['impayes'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Inscriptions validées</div>
        <div style="font-size:22px; font-weight:bold;"><?= $stats['inscriptions_validees'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Rémunérations dues</div>
        <div style="font-size:22px; font-weight:bold;"><?= $stats['remunerations'] ?></div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : comptable).
    </p>
    <p style="margin-top:12px;">
        <a href="<?= htmlspecialchars(BASE_URL . 'views/comptable/paiements.php') ?>" class="btn">Voir les paiements</a>
        <a href="<?= htmlspecialchars(BASE_URL . 'views/comptable/enregistrer_paiement.php') ?>" class="btn">Enregistrer un paiement</a>
        <a href="<?= htmlspecialchars(BASE_URL . 'views/comptable/impayes.php') ?>" class="btn">Voir les impayés</a>
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
