<?php
// includes/navbar.php
// Navbar commune/générique - GestForm
require_once __DIR__ . '/../config/app.php';
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';

$liensCommuns = [
    ['label' => 'Accueil', 'url' => BASE_URL . 'index.php', 'icon' => '🏠'],
    ['label' => 'Profil', 'url' => BASE_URL . 'shared/profil.php', 'icon' => '👤'],
];

$liensParRole = [
    'etudiant' => [
<<<<<<< HEAD
        ['label' => 'Mon dossier', 'url' => BASE_URL . 'views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Emploi du temps', 'url' => BASE_URL . 'etudiant/planning.php', 'icon' => '🗓️'],
        ['label' => 'Modules', 'url' => BASE_URL . 'etudiant/modules.php', 'icon' => '📚'],
        ['label' => 'Résultats', 'url' => BASE_URL . 'etudiant/resultats.php', 'icon' => '📊'],
=======
        ['label' => 'Mon dossier', 'url' => '/views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Emploi du temps', 'url' => '/etudiant/planning.php', 'icon' => '🗓️'],
        ['label' => 'Modules', 'url' => '/etudiant/modules.php', 'icon' => '📚'],
        ['label' => 'Résultats', 'url' => '/etudiant/resultats.php', 'icon' => '📊'],
>>>>>>> 5afcf4e06df978dbe79398a368641aed31957b21
    ],
    'formateur' => [
        ['label' => 'Mes modules', 'url' => BASE_URL . 'views/formateur/modules.php', 'icon' => '📚'],
        ['label' => 'Mes séances', 'url' => BASE_URL . 'views/formateur/seances.php', 'icon' => '🗓️'],
        ['label' => 'Évaluations', 'url' => BASE_URL . 'views/formateur/evaluations.php', 'icon' => '📝'],
        ['label' => 'Documents pédagogiques', 'url' => BASE_URL . 'views/formateur/supports.php', 'icon' => '📎'],
    ],
    'responsable' => [
        ['label' => 'Étudiants', 'url' => BASE_URL . 'responsable/etudiants.php', 'icon' => '🎓'],
        ['label' => 'Formations', 'url' => BASE_URL . 'responsable/formations.php', 'icon' => '📘'],
        ['label' => 'Planning', 'url' => BASE_URL . 'responsable/planning.php', 'icon' => '🗓️'],
    ],
    'comptable' => [
<<<<<<< HEAD
        ['label' => 'Paiements', 'url' => BASE_URL . 'views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations', 'url' => BASE_URL . 'views/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Rémunérations', 'url' => BASE_URL . 'views/comptable/remunerations.php', 'icon' => '💰'],
=======
        ['label' => 'Paiements', 'url' => '/views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations', 'url' => '/views/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Rémunérations', 'url' => '/views/comptable/remunerations.php', 'icon' => '💰'],
>>>>>>> 5afcf4e06df978dbe79398a368641aed31957b21
    ],
    'administrateur' => [
        ['label' => 'Utilisateurs', 'url' => BASE_URL . 'admin/utilisateurs.php', 'icon' => '👥'],
        ['label' => 'Rôles & accès', 'url' => BASE_URL . 'admin/roles.php', 'icon' => '🔐'],
        ['label' => 'Journal', 'url' => BASE_URL . 'admin/journal.php', 'icon' => '📜'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => BASE_URL . 'views/directeur/dashboard.php', 'icon' => '📈'],
        ['label' => 'Personnel', 'url' => BASE_URL . 'views/directeur/personnel.php', 'icon' => '👥'],
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

<script src="<?php echo htmlspecialchars(BASE_URL . 'assets/js/navebar.js'); ?>"></script>