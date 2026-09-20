<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/PaiementController.php';
require_once __DIR__ . '/../../controllers/RemunerationController.php';
$currentUser = requireRole(['comptable']);
$paiements = (new PaiementController())->getAllPaiements();
$inscriptions = (new PaiementController())->getInscriptionsValidees();
$remunerations = (new RemunerationController())->all();
$moisCourant = date('Y-m');
$paiementsMois = array_sum(array_map(static fn (array $paiement): float => str_starts_with($paiement['datePaiement'], $moisCourant) ? (float) $paiement['montant'] : 0.0, $paiements));
$impayes = count(array_filter($inscriptions, static fn (array $inscription): bool => (float) $inscription['total_paye'] <= 0));
$aPayer = count(array_filter($remunerations, static fn (array $remuneration): bool => (float) $remuneration['montantDu'] > (float) $remuneration['montantPaye']));
$solde = $paiementsMois - array_sum(array_map(static fn (array $remuneration): float => max(0, (float) $remuneration['montantDu'] - (float) $remuneration['montantPaye']), $remunerations));

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
        <div style="font-size:22px; font-weight:bold;"><?= number_format($solde, 0, ',', ' ') ?> F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : comptable).
        Les indicateurs sont calculés à partir des paiements, inscriptions et rémunérations enregistrés.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
