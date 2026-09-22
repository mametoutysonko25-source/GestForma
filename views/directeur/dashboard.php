<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../models/Directeur.php';
$currentUser = requireRole(['directeur']);
$statistiques = Directeur::statistiques();

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
$contentClass = 'director-content';
require __DIR__ . '/../../includes/header.php';
?>

<div class="director-heading">
    <div>
        <h2>Tableau de bord</h2>
        <p>Vue générale de l'activité du centre</p>
    </div>
    <strong class="director-role">Directeur</strong>
</div>

<div class="director-kpis">
    <div class="card director-kpi">
        <div class="director-kpi__label">Étudiants</div>
        <strong class="director-kpi__value"><?= (int) $statistiques['etudiants'] ?></strong>
    </div>
    <div class="card director-kpi">
        <div class="director-kpi__label">Taux de réussite</div>
        <strong class="director-kpi__value"><?= (int) $statistiques['taux_reussite'] ?>%</strong>
    </div>
    <div class="card director-kpi">
        <div class="director-kpi__label">Personnel</div>
        <strong class="director-kpi__value"><?= (int) $statistiques['personnel'] ?></strong>
    </div>
    <div class="card director-kpi">
        <div class="director-kpi__label">Finances (mois)</div>
        <strong class="director-kpi__value"><?= number_format((float) $statistiques['recettes_mois'], 0, ',', ' ') ?> F</strong>
    </div>
</div>

<div class="director-grid">
    <div class="card director-panel">
        <h3>Activité du centre</h3>
        <div class="director-chart">
            <?php foreach ([35, 58, 44, 76, 28, 52, 67] as $index => $hauteur): ?>
                <div class="director-chart__column">
                    <div class="director-chart__bar" style="height:<?= $hauteur ?>%;"></div>
                    <span class="director-chart__label">S<?= $index + 1 ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card director-panel">
        <h3>Synthèse</h3>
        <div class="director-summary">
            <div class="director-summary__row"><span>Formations</span><strong><?= (int) $statistiques['formations'] ?></strong></div>
            <div class="director-summary__row"><span>Présence</span><strong><?= (int) $statistiques['taux_presence'] ?>%</strong></div>
            <div class="director-summary__row"><span>Résultats saisis</span><strong><?= (int) $statistiques['resultats'] ?></strong></div>
            <a class="btn" href="<?php echo htmlspecialchars(BASE_URL . 'views/directeur/indicateurs.php'); ?>" style="text-align:center;">Voir les indicateurs</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
