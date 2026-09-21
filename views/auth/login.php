<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/app.php';

if (isset($_SESSION['user']['role'], DASHBOARD_PAR_ROLE[$_SESSION['user']['role']])) {
    header('Location: ' . DASHBOARD_PAR_ROLE[$_SESSION['user']['role']]);
    exit;
}
/**
 * views/auth/login.php
 * Page publique de connexion. Le traitement du formulaire (vérification
 * des identifiants, création de la session) revient au Groupe 2
 * (ex. controllers/AuthController.php, action "login").
 */
$pageTitle   = "Connexion";
$showSidebar = false;
require __DIR__ . '/../../includes/header.php';

$roles = [
    'etudiant'       => 'Étudiant',
    'formateur'      => 'Formateur',
    'responsable'    => 'Responsable',
    'comptable'      => 'Comptable',
    'administrateur' => 'Administrateur',
    'directeur'      => 'Directeur',
];
?>

<div style="display:flex; justify-content:center; padding:40px 0;">
    <div class="card" style="width:360px;">
        <h3 style="margin-top:0;">Connexion</h3>
        <p style="font-size:13px; color:var(--muted); margin-top:-10px;">Sélectionnez votre profil, puis connectez-vous</p>

        <?php if (isset($_GET['erreur'])): ?>
            <p style="background:#FDEEEE; color:#C0392B; border-radius:6px; padding:8px 12px; font-size:13px;">
                <?php
                $messages = [
                    'champs_invalides'      => 'Merci de remplir tous les champs.',
                    'identifiants_incorrects' => 'E-mail, mot de passe ou rôle incorrect.',
                    'compte_desactive'      => 'Ce compte est désactivé.',
                    'connexion_requise'     => 'Connectez-vous pour accéder à cette page.',
                    'serveur_bdd_arrete'    => 'La base de données est inaccessible. Vérifiez que MySQL/MariaDB est démarré et que la configuration de connexion est correcte.',
                    'connexion_indisponible' => 'La base de données est indisponible. Vérifiez que PDO MySQL est activé et que la configuration de connexion est correcte.',
                ];
                echo htmlspecialchars($messages[$_GET['erreur']] ?? 'Une erreur est survenue.');
                ?>
            </p>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars(BASE_URL . 'controllers/AuthController.php?action=login'); ?>">
            <div class="role-pills">
                <?php foreach ($roles as $value => $label): ?>
                    <label class="role-pill">
                        <input type="radio" name="role" value="<?= $value ?>" style="display:none;"
                               <?= $value === 'etudiant' ? 'checked' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="form-field">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" style="width:100%;">Se connecter</button>
        </form>

        <p style="text-align:center; font-size:12px; margin-top:14px;">
            <a href="<?php echo htmlspecialchars(BASE_URL . 'views/auth/mot_de_passe_oublie.php'); ?>" style="color:var(--primary);">Mot de passe oublié ?</a>
        </p>
    </div>
</div>

<script>
// Bascule visuelle simple entre les pastilles de rôle (le traitement réel
// du champ sélectionné est géré côté serveur via le name="role").
document.querySelectorAll('.role-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
        document.querySelectorAll('.role-pill').forEach(function (p) { p.classList.remove('active'); });
        pill.classList.add('active');
        pill.querySelector('input').checked = true;
    });
});
document.querySelector('.role-pill')?.classList.add('active');
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
