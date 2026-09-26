<?php
// includes/header.php
// Header commun/générique - GestForm
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/helpers.php';
$nomUtilisateur = $_SESSION['nom_utilisateur'] ?? 'Utilisateur';
$roleUtilisateur = $_SESSION['role_utilisateur'] ?? 'Invité';
$nombreNotifications = $_SESSION['nb_notifications'] ?? 0;
$showSidebar = $showSidebar ?? true;
$contentClass = $contentClass ?? '';
$flashSuccess = consumeFlash('success');
$flashError = consumeFlash('error');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestForm<?php echo isset($pageTitle) ? ' - ' . htmlspecialchars($pageTitle) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/navebar.css'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/sidebar.css'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/auth.css'); ?>">
    <?php if ($contentClass === 'director-content' || $contentClass === 'management-content'): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/director-content.css'); ?>">
    <?php endif; ?>
    <?php if ($contentClass === 'director-content' || $contentClass === 'management-content'): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/management-content.css'); ?>">
    <?php endif; ?>
    <?php if ($contentClass === 'home-page'): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL . 'assets/css/home.css'); ?>">
    <?php endif; ?>
</head>

<body>

    <header class="gf-header">
        <div class="gf-header__left">
            <a href="<?php echo htmlspecialchars(BASE_URL); ?>" class="gf-header__logo">
                <span class="gf-header__logo-mark" aria-hidden="true">G</span>
                <span class="gf-header__logo-text">GestForm</span>
            </a>
        </div>

        <div class="gf-header__right">
            <?php if (isset($_SESSION['user'])): ?>
                <button class="gf-header__icon-btn" id="btn-notifications" aria-label="Notifications">
                    🔔
                    <?php if ($nombreNotifications > 0): ?>
                        <span class="gf-badge"><?php echo (int) $nombreNotifications; ?></span>
                    <?php endif; ?>
                </button>
                <div class="gf-header__user">
                    <span class="gf-header__user-name"><?php echo htmlspecialchars($nomUtilisateur); ?></span>
                    <span class="gf-header__user-role"><?php echo htmlspecialchars($roleUtilisateur); ?></span>
                </div>
                <div class="gf-header__dropdown">
                    <button class="gf-header__avatar-btn" id="btn-user-menu" aria-label="Menu utilisateur">
                        <span class="gf-header__avatar" aria-hidden="true"><?php echo htmlspecialchars(strtoupper(substr($nomUtilisateur, 0, 1))); ?></span>
                    </button>
                    <ul class="gf-header__dropdown-menu" id="user-menu">
                        <li><a href="<?php echo htmlspecialchars(BASE_URL . 'shared/profil.php'); ?>">Mon profil</a></li>
                        <li><a href="<?php echo htmlspecialchars(BASE_URL . 'shared/parametres.php'); ?>">Paramètres</a></li>
                        <li><a href="<?php echo htmlspecialchars(BASE_URL . 'controllers/AuthController.php?action=logout'); ?>">Déconnexion</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="gf-header__login" href="<?php echo htmlspecialchars(BASE_URL . 'views/auth/login.php'); ?>">Connexion</a>
            <?php endif; ?>
        </div>
    </header>

    <script src="<?php echo htmlspecialchars(BASE_URL . 'assets/js/header.js'); ?>"></script>
    <?php if ($showSidebar): ?>
        <?php require __DIR__ . '/navebar.php'; ?>
        <div class="gf-layout">
            <?php require __DIR__ . '/sidebar.php'; ?>
            <main class="gf-content <?= htmlspecialchars($contentClass) ?>">
                <?php if ($flashSuccess): ?><div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px; color:#1b5e20;" role="status"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
                <?php if ($flashError): ?><div class="card" style="border-left:4px solid #c62828; margin-bottom:16px; color:#b71c1c;" role="alert"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
    <?php endif; ?>