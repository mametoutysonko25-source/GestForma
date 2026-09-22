<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['formateur']);
$db = database(); $id = (int) $currentUser['id'];
$statistiques = ['modules' => 0, 'seances' => 0, 'copies' => 0, 'remuneration' => 0];
$q = $db->prepare('SELECT COUNT(*) FROM module WHERE idFormateur = :id'); $q->execute(['id'=>$id]); $statistiques['modules']=(int)$q->fetchColumn();
$q = $db->prepare('SELECT COUNT(*) FROM seance WHERE idFormateur = :id AND dateSeance >= CURRENT_DATE()'); $q->execute(['id'=>$id]); $statistiques['seances']=(int)$q->fetchColumn();
$q = $db->prepare('SELECT COUNT(*) FROM depot d INNER JOIN evaluation e ON e.idEvaluation=d.idEvaluation INNER JOIN module m ON m.idModule=e.idModule WHERE m.idFormateur=:id'); $q->execute(['id'=>$id]); $statistiques['copies']=(int)$q->fetchColumn();
$q = $db->prepare('SELECT COALESCE(SUM(montantDu),0) FROM remuneration_formateur WHERE idFormateur=:id AND mois=DATE_FORMAT(CURRENT_DATE(), "%Y-%m")'); $q->execute(['id'=>$id]); $statistiques['remuneration']=(float)$q->fetchColumn();

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['modules'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Séances cette semaine</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['seances'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Copies à corriger</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['copies'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Rémunération (mois)</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format($statistiques['remuneration'], 0, ',', ' ') ?> F</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : formateur).
        Vos données sont calculées à partir des modules, séances, dépôts et rémunérations qui vous sont affectés.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
