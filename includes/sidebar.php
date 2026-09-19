<?php
// includes/sidebar.php
// Sidebar commune/générique - GestForm
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';
$pageActuelle = basename($_SERVER['PHP_SELF'] ?? '');

$menuParRole = [
    'etudiant' => [
        ['label' => 'Tableau de bord', 'url' => '/views/etudiant/dashboard.php', 'icon' => '📊'],
        ['label' => 'Mon dossier', 'url' => '/views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Demande d’inscription', 'url' => '/views/etudiant/demande_inscription.php', 'icon' => '📝'],
        ['label' => 'État de l’inscription', 'url' => '/views/etudiant/etat_inscription.php', 'icon' => '📋'],
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
        ['label' => 'Paiements étudiants', 'url' => '/views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Situations financières', 'url' => '/views/comptable/situations.php', 'icon' => '📊'],
        ['label' => 'Impayés', 'url' => '/views/comptable/impayes.php', 'icon' => '⚠️'],
        ['label' => 'Rémunérations', 'url' => '/views/comptable/remunerations.php', 'icon' => '💰'],
    ],
    'administrateur' => [
        ['label' => 'Tableau de bord', 'url' => '/views/administrateur/dashboard.php', 'icon' => '📊'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => '/views/directeur/dashboard.php', 'icon' => '📈'],
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