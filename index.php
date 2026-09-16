<?php
// index.php - Page d'exemple pour tester les includes du Groupe 3
session_start();



$_SESSION['nom_utilisateur'] = 'Awa Fall';
$_SESSION['role_utilisateur'] = 'responsable_pedagogique';


$titrePage = 'Tableau de bord';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navebar.php';
?>

<div class="gf-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="gf-content">
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?> 👋</h1>
        <p>Ceci est une page d'exemple pour vérifier que le Header, la Navbar, la Sidebar et le Footer s'affichent
            correctement ensemble.</p>
        <p>Rôle actuel : <strong><?php echo htmlspecialchars($_SESSION['role_utilisateur']); ?></strong></p>
    </main>
</div>

<?php include 'includes/footer.php'; ?>