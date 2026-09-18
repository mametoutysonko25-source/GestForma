<?php
// includes/footer.php
// Footer commun/générique - GestForm
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
        <a href="/a-propos.php">À propos</a>
        <a href="/aide.php">Aide</a>
        <a href="/contact.php">Contact</a>
        <a href="/mentions-legales.php">Mentions légales</a>
    </div>

    <div class="gf-footer__right">
        <span class="gf-footer__version">v1.0</span>
    </div>
</footer>

</body>

</html>