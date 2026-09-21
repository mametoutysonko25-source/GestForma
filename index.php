<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/app.php';

if (isset($_SESSION['user']['role'], DASHBOARD_PAR_ROLE[$_SESSION['user']['role']])) {
    header('Location: ' . DASHBOARD_PAR_ROLE[$_SESSION['user']['role']]);
    exit;
}

$pageTitle = 'Accueil';
$showSidebar = false;
require __DIR__ . '/includes/header.php';
?>

<section style="background:var(--primary-light); margin:-24px -24px 32px -24px; padding:48px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
    <div style="max-width:480px;">
        <h1 style="font-size:32px; margin:0 0 6px 0;">Simplifiez la gestion</h1>
        <h1 style="font-size:32px; margin:0 0 16px 0; color:var(--primary);">de vos formations</h1>
        <p style="color:var(--muted);">
            Suivez vos inscriptions, emplois du temps, présences, évaluations et paiements
            depuis une interface unique, adaptée aux étudiants, formateurs, responsables
            pédagogiques et comptables.
        </p>
    </div>
    <div class="card" style="width:280px;">
        <h3 style="margin-top:0;">Connexion</h3>
        <p style="font-size:13px; color:var(--muted);">Accédez à votre espace</p>
        <a href="<?php echo htmlspecialchars(BASE_URL . 'views/auth/login.php'); ?>" class="btn" style="display:block; text-align:center;">Se connecter</a>
    </div>
</section>

<section id="metier">
    <h2 style="text-align:center; color:var(--primary);">Une interface pour chaque profil</h2>
    <p style="text-align:center; color:var(--muted);">Chaque utilisateur dispose d'un espace dédié avec des fonctionnalités adaptées à son rôle</p>

    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; margin-top:24px;">
        <div class="card">
            <h4>Espace Étudiant</h4>
            <p style="font-size:13px; color:var(--muted);">Demande d'inscription, emploi du temps et présence, évaluations et notes, suivi des paiements, notifications.</p>
        </div>
        <div class="card">
            <h4>Espace Formateur</h4>
            <p style="font-size:13px; color:var(--muted);">Consultation des séances, appel & présences, création d'évaluations, correction des copies, situation financière.</p>
        </div>
        <div class="card">
            <h4>Espace Responsable pédagogique</h4>
            <p style="font-size:13px; color:var(--muted);">Traitement des inscriptions, gestion étudiants & formateurs, gestion formations/modules, planification des séances, emploi du temps global.</p>
        </div>
        <div class="card">
            <h4>Espace Comptable</h4>
            <p style="font-size:13px; color:var(--muted);">Situation financière étudiants, enregistrement des paiements, historique des transactions, paiement des formateurs.</p>
        </div>
        <div class="card">
            <h4>Administration</h4>
            <p style="font-size:13px; color:var(--muted);">Gestion des utilisateurs, gestion des rôles et permissions, paramètres système, journal des actions.</p>
        </div>
        <div class="card">
            <h4>Espace Directeur</h4>
            <p style="font-size:13px; color:var(--muted);">Tableau de bord général, ajout d'une formation, validation des dossiers étudiants et formateurs, consultation des rapports d'activité.</p>
        </div>
    </div>
</section>

<section id="contact" style="margin-top:40px; text-align:center; color:var(--muted); font-size:13px;">
    <p>Contact — centre de formation • contact@gestform.example</p>
</section>

<?php include 'includes/footer.php'; ?>