<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/app.php';

if (isset($_SESSION['user']['role'], DASHBOARD_PAR_ROLE[$_SESSION['user']['role']])) {
    header('Location: ' . DASHBOARD_PAR_ROLE[$_SESSION['user']['role']]);
    exit;
}

$pageTitle = 'Accueil';
$showSidebar = false;
$contentClass = 'home-page';
$heroImagePath = __DIR__ . '/assets/images/images.jpg';
$heroImageUrl = is_file($heroImagePath) ? BASE_URL . 'assets/images/images.jpg' : null;
require __DIR__ . '/includes/header.php';
?>

<main class="gf-home">
    <section class="gf-home__hero" aria-labelledby="home-title"<?= $heroImageUrl ? ' style="--gf-hero-image: url(&quot;' . htmlspecialchars($heroImageUrl, ENT_QUOTES, 'UTF-8') . '&quot;)"' : '' ?>>
        <div class="gf-home__hero-inner">
            <div class="gf-home__intro">
                <p class="gf-home__eyebrow">CEFAS · Centre de formation</p>
                <h1 id="home-title">La formation,<br><span>mieux organisée.</span></h1>
                <p class="gf-home__lead">Un espace de travail clair pour suivre les cours, accompagner les étudiants et piloter votre centre au quotidien.</p>
                <a href="<?= htmlspecialchars(BASE_URL . 'views/auth/login.php') ?>" class="gf-home__cta">Accéder à mon espace <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="gf-home__hero-note" aria-label="Une plateforme pour toute l’équipe">
                <span class="gf-home__note-mark" aria-hidden="true">GF</span>
                <span><strong>Un centre, une équipe.</strong><br>Tout votre suivi pédagogique au même endroit.</span>
            </div>
        </div>
        <div class="gf-home__hero-caption"><span>01</span> Formation · Suivi · Gestion</div>
    </section>

    <section class="gf-home__spaces" id="metier" aria-labelledby="spaces-title">
        <div class="gf-home__section-heading">
            <div>
                <p class="gf-home__eyebrow">Des outils adaptés à votre rôle</p>
                <h2 id="spaces-title">Votre espace de travail</h2>
            </div>
            <p>Les principales activités du centre, réunies dans une seule plateforme.</p>
        </div>

        <div class="gf-home__grid">
            <article class="gf-home__space gf-home__space--student"><span class="gf-home__space-index">01</span><h3>Étudiants</h3><p>Inscriptions, emploi du temps, présences, résultats et suivi des paiements.</p></article>
            <article class="gf-home__space gf-home__space--trainer"><span class="gf-home__space-index">02</span><h3>Formateurs</h3><p>Séances, feuilles d’appel, évaluations, corrections et supports de cours.</p></article>
            <article class="gf-home__space gf-home__space--academic"><span class="gf-home__space-index">03</span><h3>Responsables pédagogiques</h3><p>Inscriptions, équipes, formations, modules et planification des séances.</p></article>
            <article class="gf-home__space gf-home__space--finance"><span class="gf-home__space-index">04</span><h3>Comptabilité</h3><p>Paiements étudiants, impayés, situations financières et rémunérations.</p></article>
            <article class="gf-home__space gf-home__space--admin"><span class="gf-home__space-index">05</span><h3>Administration</h3><p>Comptes utilisateurs, rôles, accès et journal des activités.</p></article>
            <article class="gf-home__space gf-home__space--director"><span class="gf-home__space-index">06</span><h3>Direction</h3><p>Indicateurs d’activité, suivi du personnel et informations du centre.</p></article>
        </div>
    </section>

    <section class="gf-home__contact" id="contact">
        <p class="gf-home__eyebrow">Besoin d’aide ?</p>
        <p>Notre équipe est à votre écoute : <a href="mailto:contact@gestform.example">contact@gestform.example</a></p>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>