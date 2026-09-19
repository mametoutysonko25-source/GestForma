<?php
// includes/navbar.php
// Navbar commune/générique - GestForm
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';

$liensCommuns = [
    ['label' => 'Accueil', 'url' => '/index.php', 'icon' => '🏠'],
];

$liensParRole = [
    'etudiant' => [
        ['label' => 'Tableau de bord', 'url' => '/views/etudiant/dashboard.php', 'icon' => '📊'],
        ['label' => 'Mon dossier', 'url' => '/views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Demande d’inscription', 'url' => '/views/etudiant/demande_inscription.php', 'icon' => '📝'],
        ['label' => 'Situation financière', 'url' => '/views/etudiant/finance.php', 'icon' => '💳'],
    ],
    'formateur' => [
        ['label' => 'Tableau de bord', 'url' => '/views/formateur/dashboard.php', 'icon' => '📊'],
    ],
    'responsable' => [
        ['label' => 'Tableau de bord', 'url' => '/views/responsable/dashboard.php', 'icon' => '📊'],
        ['label' => 'Valider les inscriptions', 'url' => '/views/responsable/valider_inscriptions.php', 'icon' => '✅'],
    ],
    'comptable' => [
        ['label' => 'Paiements', 'url' => '/views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations', 'url' => '/views/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Rémunérations', 'url' => '/views/comptable/remunerations.php', 'icon' => '💰'],
    ],
    'administrateur' => [
        ['label' => 'Tableau de bord', 'url' => '/views/administrateur/dashboard.php', 'icon' => '📊'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => '/views/directeur/dashboard.php', 'icon' => '📈'],
    ],
];

$liensRole = $liensParRole[$roleUtilisateur] ?? [];
$pageActuelle = basename($_SERVER['PHP_SELF'] ?? '');
?>
<nav class="gf-navbar">
    <button class="gf-navbar__toggle" id="navbar-toggle" aria-label="Ouvrir le menu">
        ☰
    </button>

    <ul class="gf-navbar__menu" id="navbar-menu">
        <?php foreach ($liensCommuns as $lien): ?>
            <li class="gf-navbar__item <?php echo $pageActuelle === basename($lien['url']) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($lien['url']); ?>">
                    <span class="gf-navbar__icon"><?php echo $lien['icon']; ?></span>
                    <span><?php echo htmlspecialchars($lien['label']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if (!empty($liensRole)): ?>
            <li class="gf-navbar__separator"></li>
            <?php foreach ($liensRole as $lien): ?>
                <li class="gf-navbar__item <?php echo $pageActuelle === basename($lien['url']) ? 'active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($lien['url']); ?>">
                        <span class="gf-navbar__icon"><?php echo $lien['icon']; ?></span>
                        <span><?php echo htmlspecialchars($lien['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</nav>

<script src="/assets/js/navebar.js"></script>