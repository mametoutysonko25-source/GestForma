<?php
// includes/sidebar.php
// Sidebar commune/générique - GestForm
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';
$pageActuelle = basename($_SERVER['PHP_SELF'] ?? '');

$menuParRole = [
    'etudiant' => [
        ['label' => 'Tableau de bord', 'url' => '/views/etudiant/dashboard.php', 'icon' => '📊'],
        ['label' => 'Mon dossier', 'url' => '/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Emploi du temps', 'url' => '/etudiant/planning.php', 'icon' => '🗓️'],
        ['label' => 'Modules', 'url' => '/etudiant/modules.php', 'icon' => '📚'],
        ['label' => 'Supports de cours', 'url' => '/etudiant/supports.php', 'icon' => '📄'],
        ['label' => 'Évaluations', 'url' => '/etudiant/evaluations.php', 'icon' => '📝'],
        ['label' => 'Dépôt de travail', 'url' => '/etudiant/depot.php', 'icon' => '📤'],
        ['label' => 'Résultats', 'url' => '/etudiant/resultats.php', 'icon' => '🏆'],
        ['label' => 'Situation financière', 'url' => '/etudiant/finance.php', 'icon' => '💳'],
    ],
    'formateur' => [
        ['label' => 'Mes modules', 'url' => '/formateur/modules.php', 'icon' => '📚'],
        ['label' => 'Mes séances', 'url' => '/formateur/seances.php', 'icon' => '🗓️'],
        ['label' => 'Feuille d\'appel', 'url' => '/formateur/appel.php', 'icon' => '✅'],
        ['label' => 'Évaluations', 'url' => '/formateur/evaluations.php', 'icon' => '📝'],
        ['label' => 'Travaux à corriger', 'url' => '/formateur/correction.php', 'icon' => '📑'],
        ['label' => 'Saisie des notes', 'url' => '/formateur/notes.php', 'icon' => '✏️'],
        ['label' => 'Rémunération', 'url' => '/formateur/remuneration.php', 'icon' => '💰'],
    ],
    'responsable' => [
        ['label' => 'Étudiants', 'url' => '/responsable/etudiants.php', 'icon' => '🎓'],
        ['label' => 'Inscriptions', 'url' => '/responsable/inscriptions.php', 'icon' => '📥'],
        ['label' => 'Formations', 'url' => '/responsable/formations.php', 'icon' => '📘'],
        ['label' => 'Niveaux & semestres', 'url' => '/responsable/niveaux.php', 'icon' => '🎯'],
        ['label' => 'Modules', 'url' => '/responsable/modules.php', 'icon' => '📚'],
        ['label' => 'Affectations', 'url' => '/responsable/affectation.php', 'icon' => '👥'],
        ['label' => 'Planning', 'url' => '/responsable/planning.php', 'icon' => '🗓️'],
        ['label' => 'Présences', 'url' => '/responsable/presences.php', 'icon' => '✅'],
        ['label' => 'Résultats', 'url' => '/responsable/resultats.php', 'icon' => '📊'],
    ],

    'comptable' => [
        ['label' => 'Paiements étudiants', 'url' => '/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations financières', 'url' => '/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Impayés', 'url' => '/comptable/impayes.php', 'icon' => '⚠️'],
        ['label' => 'Rémunérations', 'url' => '/comptable/remunerations.php', 'icon' => '💰'],
    ],
    'administrateur' => [
        ['label' => 'Utilisateurs', 'url' => '/admin/utilisateurs.php', 'icon' => '👥'],
        ['label' => 'Rôles & accès', 'url' => '/admin/roles.php', 'icon' => '🔐'],
        ['label' => 'Paramètres', 'url' => '/admin/parametres.php', 'icon' => '⚙️'],
        ['label' => 'Journal des actions', 'url' => '/admin/journal.php', 'icon' => '📜'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => '/directeur/dashboard.php', 'icon' => '📈'],
        ['label' => 'Indicateurs', 'url' => '/directeur/indicateurs.php', 'icon' => '📊'],
        ['label' => 'Personnel', 'url' => '/directeur/personnel.php', 'icon' => '👥'],
        ['label' => 'Infos du centre', 'url' => '/directeur/infos.php', 'icon' => 'ℹ️'],
    ],
];

$menuItems = $menuParRole[$roleUtilisateur] ?? [];
?>
<aside class="gf-sidebar" id="gf-sidebar">
    <button class="gf-sidebar__collapse-btn" id="sidebar-collapse-btn" aria-label="Réduire le menu">
        «
    </button>

    <ul class="gf-sidebar__menu">
        <?php foreach ($menuItems as $item): ?>
            <li class="gf-sidebar__item <?php echo $pageActuelle === basename($item['url']) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($item['url']); ?>"
                    title="<?php echo htmlspecialchars($item['label']); ?>">
                    <span class="gf-sidebar__icon"><?php echo $item['icon']; ?></span>
                    <span class="gf-sidebar__label"><?php echo htmlspecialchars($item['label']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if (empty($menuItems)): ?>
            <li class="gf-sidebar__item gf-sidebar__item--empty">
                <span class="gf-sidebar__label">Aucun menu pour ce rôle</span>
            </li>
        <?php endif; ?>
    </ul>
</aside>

<script src="/assets/js/sidebar.js"></script>