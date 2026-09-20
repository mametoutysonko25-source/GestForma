<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../models/Directeur.php';
requireRole(['directeur']);
$statistiques = Directeur::statistiques();
$pageTitle = 'Indicateurs';
$activeMenu = 'indicateurs';
$contentClass = 'director-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Indicateurs</h2><p>Analyse synthétique de l'activité du centre.</p></div></div>
<div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px;">
    <div class="card director-panel"><h3>Pédagogie</h3><div class="director-summary"><div class="director-summary__row"><span>Étudiants</span><strong><?= (int) $statistiques['etudiants'] ?></strong></div><div class="director-summary__row"><span>Formations</span><strong><?= (int) $statistiques['formations'] ?></strong></div><div class="director-summary__row"><span>Taux de réussite</span><strong><?= (int) $statistiques['taux_reussite'] ?>%</strong></div><div class="director-summary__row"><span>Taux de présence</span><strong><?= (int) $statistiques['taux_presence'] ?>%</strong></div></div></div>
    <div class="card director-panel"><h3>Personnel</h3><div class="director-summary"><div class="director-summary__row"><span>Total</span><strong><?= (int) $statistiques['personnel'] ?></strong></div><div class="director-summary__row"><span>Formateurs</span><strong><?= (int) $statistiques['formateurs'] ?></strong></div><div class="director-summary__row"><span>Responsables</span><strong><?= (int) $statistiques['responsables'] ?></strong></div><div class="director-summary__row"><span>Comptables</span><strong><?= (int) $statistiques['comptables'] ?></strong></div></div></div>
    <div class="card director-panel"><h3>Finances</h3><div class="director-summary__row"><span>Recettes du mois</span><strong><?= number_format((float) $statistiques['recettes_mois'], 0, ',', ' ') ?> F</strong></div></div>
    <div class="card director-panel"><h3>Données disponibles</h3><div class="director-summary"><div class="director-summary__row"><span>Résultats saisis</span><strong><?= (int) $statistiques['resultats'] ?></strong></div><div class="director-summary__row"><span>Présences</span><strong><?= (int) $statistiques['presences'] ?></strong></div></div></div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>