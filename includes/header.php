<?php
// includes/header.php
// Header commun/générique - GestForm
$nomUtilisateur = $_SESSION['nom_utilisateur'] ?? 'Utilisateur';
$roleUtilisateur = $_SESSION['role_utilisateur'] ?? 'Invité';
$nombreNotifications = $_SESSION['nb_notifications'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestForm<?php echo isset($titrePage) ? ' - ' . htmlspecialchars($titrePage) : ''; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

    <header class="gf-header">
        <div class="gf-header__left">
            <a href="/" class="gf-header__logo">
                <img src="assets/img/logo.jpeg" alt="GestForm" class="gf-header__logo-img">
                <span class="gf-header__logo-text">GestForm</span>
            </a>
        </div>

        <div class="gf-header__right">
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
                    <img src="assets/img/avatar-default.png" alt="Avatar" class="gf-header__avatar">
                </button>
                <ul class="gf-header__dropdown-menu" id="user-menu">
                    <li><a href="/profil.php">Mon profil</a></li>
                    <li><a href="/parametres.php">Paramètres</a></li>
                    <li><a href="/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </header>

    <script src="assets/js/header.js"></script>