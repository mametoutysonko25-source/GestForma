<?php
// includes/sidebar.php
// Sidebar commune/générique - GestForm
require_once __DIR__ . '/../config/app.php';
$roleUtilisateur = $_SESSION['user']['role'] ?? $_SESSION['role_utilisateur'] ?? 'invite';
$pageActuelle = basename($_SERVER['PHP_SELF'] ?? '');

$menuParRole = [
    'etudiant' => [
        ['label' => 'Tableau de bord', 'url' => BASE_URL . 'views/etudiant/dashboard.php', 'icon' => '📊'],
        ['label' => 'Mon dossier', 'url' => BASE_URL . 'views/etudiant/dossier.php', 'icon' => '📁'],
        ['label' => 'Demande d\'inscription', 'url' => BASE_URL . 'views/etudiant/demande_inscription.php', 'icon' => '📝'],
        ['label' => 'État de l\'inscription', 'url' => BASE_URL . 'views/etudiant/etat_inscription.php', 'icon' => '✅'],
        ['label' => 'Situation financière', 'url' => BASE_URL . 'views/etudiant/finance.php', 'icon' => '💳'],
    ],
    'formateur' => [
        ['label' => 'Mes modules', 'url' => BASE_URL . 'views/formateur/modules.php', 'icon' => '📚'],
        ['label' => 'Mes séances', 'url' => BASE_URL . 'views/formateur/seances.php', 'icon' => '🗓️'],
        ['label' => 'Feuille d\'appel', 'url' => BASE_URL . 'views/formateur/appel.php', 'icon' => '✅'],
        ['label' => 'Évaluations', 'url' => BASE_URL . 'views/formateur/evaluations.php', 'icon' => '📝'],
        ['label' => 'Travaux à corriger', 'url' => BASE_URL . 'views/formateur/correction.php', 'icon' => '📑'],
        ['label' => 'Saisie des notes', 'url' => BASE_URL . 'views/formateur/notes.php', 'icon' => '✏️'],
        ['label' => 'Documents pédagogiques', 'url' => BASE_URL . 'views/formateur/supports.php', 'icon' => '📎'],
        ['label' => 'Rémunération', 'url' => BASE_URL . 'views/formateur/remuneration.php', 'icon' => '💰'],
    ],
    'responsable' => [
        ['label' => 'Tableau de bord', 'url' => BASE_URL . 'views/responsable/dashboard.php', 'icon' => '📊'],
        ['label' => 'Étudiants', 'url' => BASE_URL . 'views/responsable/etudiants.php', 'icon' => '🎓'],
        ['label' => 'Inscriptions', 'url' => BASE_URL . 'views/responsable/inscriptions.php', 'icon' => '📥'],
        ['label' => 'Formateurs', 'url' => BASE_URL . 'views/responsable/formateurs.php', 'icon' => '👨‍🏫'],
        ['label' => 'Formations', 'url' => BASE_URL . 'views/responsable/formations.php', 'icon' => '📘'],
        ['label' => 'Niveaux', 'url' => BASE_URL . 'views/responsable/niveaux.php', 'icon' => '🎯'],
        ['label' => 'Semestres', 'url' => BASE_URL . 'views/responsable/semestres.php', 'icon' => '📆'],
        ['label' => 'Modules', 'url' => BASE_URL . 'views/responsable/modules.php', 'icon' => '📚'],
        ['label' => 'Affectations', 'url' => BASE_URL . 'views/responsable/affectation.php', 'icon' => '👥'],
        ['label' => 'Planning', 'url' => BASE_URL . 'views/responsable/planning.php', 'icon' => '🗓️'],
        ['label' => 'Présences', 'url' => BASE_URL . 'views/responsable/presences.php', 'icon' => '✅'],
        ['label' => 'Feuille d\'appel', 'url' => BASE_URL . 'views/formateur/appel.php', 'icon' => '📋'],
        ['label' => 'Résultats', 'url' => BASE_URL . 'views/responsable/resultats.php', 'icon' => '📊'],
    ],

    'comptable' => [
        ['label' => 'Tableau de bord', 'url' => BASE_URL . 'views/comptable/dashboard.php', 'icon' => '📊'],
        ['label' => 'Paiements étudiants', 'url' => BASE_URL . 'views/comptable/paiements.php', 'icon' => '💳'],
        ['label' => 'Enregistrer paiement', 'url' => BASE_URL . 'views/comptable/enregistrer_paiement.php', 'icon' => '🧾'],
        ['label' => 'Impayés', 'url' => BASE_URL . 'views/comptable/impayes.php', 'icon' => '⚠️'],
        ['label' => 'Situations financières', 'url' => BASE_URL . 'views/comptable/situations.php', 'icon' => '📉'],
        ['label' => 'Rémunérations', 'url' => BASE_URL . 'views/comptable/remunerations.php', 'icon' => '💰'],
    ],
    'administrateur' => [
        ['label' => 'Utilisateurs', 'url' => BASE_URL . 'admin/utilisateurs.php', 'icon' => '👥'],
        ['label' => 'Rôles & accès', 'url' => BASE_URL . 'admin/roles.php', 'icon' => '🔐'],
        ['label' => 'Paramètres', 'url' => BASE_URL . 'admin/parametres.php', 'icon' => '⚙️'],
        ['label' => 'Journal des actions', 'url' => BASE_URL . 'admin/journal.php', 'icon' => '📜'],
    ],
    'directeur' => [
        ['label' => 'Tableau de bord', 'url' => BASE_URL . 'views/directeur/dashboard.php', 'icon' => '📈'],
        ['label' => 'Indicateurs', 'url' => BASE_URL . 'views/directeur/indicateurs.php', 'icon' => '📊'],
        ['label' => 'Personnel', 'url' => BASE_URL . 'views/directeur/personnel.php', 'icon' => '👥'],
        ['label' => 'Infos du centre', 'url' => BASE_URL . 'views/directeur/infos.php', 'icon' => 'ℹ️'],
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

<script src="<?php echo htmlspecialchars(BASE_URL . 'assets/js/sidebar.js'); ?>"></script>