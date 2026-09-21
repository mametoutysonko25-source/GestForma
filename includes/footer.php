<?php
// includes/footer.php
// Footer commun/générique - GestForm
require_once __DIR__ . '/../config/app.php';
$anneeActuelle = date('Y');
?>
<?php if (!empty($showSidebar)): ?>
            </main>
        </div>
<?php endif; ?>
<footer class="gf-footer">
    <div class="gf-footer__left">
        <span>&copy; <?php echo $anneeActuelle; ?> GestForm — CEFAS. Tous droits réservés.</span>
    </div>

    <div class="gf-footer__links">
        <a href="<?php echo htmlspecialchars(BASE_URL . 'shared/a-propos.php'); ?>">À propos</a>
        <a href="<?php echo htmlspecialchars(BASE_URL . 'shared/aide.php'); ?>">Aide</a>
        <a href="<?php echo htmlspecialchars(BASE_URL . 'shared/contact.php'); ?>">Contact</a>
        <a href="<?php echo htmlspecialchars(BASE_URL . 'shared/mentions-legales.php'); ?>">Mentions légales</a>
    </div>

    <div class="gf-footer__right">
        <span class="gf-footer__version">v1.0</span>
    </div>
</footer>

</body>

</html>