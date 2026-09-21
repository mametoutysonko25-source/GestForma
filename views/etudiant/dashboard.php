<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
$currentUser = requireRole(['etudiant']);

$statement = database()->prepare('SELECT COUNT(DISTINCT m.idModule) AS modules,
    COALESCE((SELECT SUM(p2.montant) FROM paiement p2 WHERE p2.idInscription = i.idInscription), 0) AS totalPaye,
    COUNT(DISTINCT r.idResultat) AS resultats
    FROM dossier_etudiant d
    LEFT JOIN inscription i ON i.idDossier = d.idDossier
    LEFT JOIN niveau n ON n.idNiveau = i.idNiveau
    LEFT JOIN semestre s ON s.idNiveau = n.idNiveau
    LEFT JOIN module m ON m.idSemestre = s.idSemestre
    LEFT JOIN paiement p ON p.idInscription = i.idInscription
    LEFT JOIN resultat r ON r.idEtudiant = d.idEtudiant
    WHERE d.idEtudiant = :id AND i.statut = "VALIDEE"');
$statement->execute(['id' => $currentUser['id']]);
$indicateurs = $statement->fetch();

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard"; // doit correspondre à une clé du menu dans includes/sidebar.php
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;"><?= (int) ($indicateurs['modules'] ?? 0) ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Présences</div>
        <div style="font-size:22px; font-weight:bold;">-</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Moyenne</div>
        <div style="font-size:22px; font-weight:bold;"><?= (int) ($indicateurs['resultats'] ?? 0) ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Solde</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format((float) ($indicateurs['totalPaye'] ?? 0), 0, ',', ' ') ?> F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Vos indicateurs sont calculés à partir de votre dossier, de votre inscription et de vos paiements.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
