<?php
// includes/navbar.php
// Navbar commune/générique - GestForm
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';

$liensCommuns = [
    ['label' => 'Accueil', 'url' => '/index.php', 'icon' => '🏠'],
    ['label' => 'Profil', 'url' => '/profil.php', 'icon' => '👤'],
];

$liensParRole = [
    'etudiant' => [
        ['label' => 'Mon dossier', 'url' => '/views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Emploi du temps', 'url' => '/etudiant/planning.php', 'icon' => '🗓️'],
        ['label' => 'Modules', 'url' => '/etudiant/modules.php', 'icon' => '📚'],
        ['label' => 'Résultats', 'url' => '/etudiant/resultats.php', 'icon' => '📊'],
    ],
    'formateur' => [
        ['label' => 'Mes modules', 'url' => '/formateur/modules.php', 'icon' => '📚'],
        ['label' => 'Mes séances', 'url' => '/formateur/seances.php', 'icon' => '🗓️'],
        ['label' => 'Évaluations', 'url' => '/formateur/evaluations.php', 'icon' => '📝'],
    ],
    'responsable' => [
        ['label' => 'Étudiants', 'url' => '/responsable/etudiants.php', 'icon' => '🎓'],
        ['label' => 'Formations', 'url' => '/responsable/formations.php', 'icon' => '📘'],
        ['label' => 'Planning', 'url' => '/responsable/planning.php', 'icon' => '🗓️'],
    ],
    'comptable' => [
        ['label' => 'Paiements', 'url' => '/views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations', 'url' => '/views/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Rémunérations', 'url' => '/views/comptable/remunerations.php', 'icon' => '💰'],
    ],
    'administrateur' => [
        ['label' => 'Utilisateurs', 'url' => '/admin/utilisateurs.php', 'icon' => '👥'],
        ['label' => 'Rôles & accès', 'url' => '/admin/roles.php', 'icon' => '🔐'],
        ['label' => 'Journal', 'url' => '/admin/journal.php', 'icon' => '📜'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => '/directeur/dashboard.php', 'icon' => '📈'],
        ['label' => 'Personnel', 'url' => '/directeur/personnel.php', 'icon' => '👥'],
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

<script src="<?= htmlspecialchars(BASE_URL . 'assets/js/navebar.js') ?>"></script>