<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);
$db = database();
$statistiques = [
    'utilisateurs' => (int) $db->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn(),
    'inactifs' => (int) $db->query("SELECT COUNT(*) FROM utilisateur WHERE statutCompte <> 'ACTIF'")->fetchColumn(),
    'roles' => (int) $db->query('SELECT COUNT(*) FROM (SELECT idUtilisateur FROM administrateur UNION SELECT idUtilisateur FROM directeur UNION SELECT idUtilisateur FROM responsable_pedagogique UNION SELECT idUtilisateur FROM formateur UNION SELECT idUtilisateur FROM comptable UNION SELECT idUtilisateur FROM etudiant) roles')->fetchColumn(),
    'actions' => (int) $db->query('SELECT COUNT(*) FROM historique WHERE DATE(dateAction) = CURRENT_DATE()')->fetchColumn(),
];

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Utilisateurs</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['utilisateurs'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Comptes désactivés</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['inactifs'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Rôles configurés</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['roles'] ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Actions aujourd'hui</div>
        <div style="font-size:22px; font-weight:bold;"><?= $statistiques['actions'] ?></div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Connexion réussie en tant que <strong><?= htmlspecialchars($currentUser['nom']) ?></strong> (rôle : administrateur).
        Utilisez le menu pour administrer les comptes, contrôler les rôles et consulter les actions enregistrées.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
