<?php
<<<<<<< HEAD
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
                    'serveur_bdd_arrete'    => 'Le serveur MySQL est arrêté. Démarrez le service MySQL80 puis réessayez.',
                    'connexion_indisponible' => 'La base de données est indisponible. Activez PDO MySQL puis réessayez.',
                ];
                echo htmlspecialchars($messages[$_GET['erreur']] ?? 'Une erreur est survenue.');
                ?>
            </p>
        <?php endif; ?>

        <form method="post" action="/controllers/AuthController.php?action=login">
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
            <a href="/views/auth/mot_de_passe_oublie.php" style="color:var(--primary);">Mot de passe oublié ?</a>
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
=======
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/Router.php';

// Rôles autorisés à se connecter depuis cette interface
$rolesAutorises = [
    'etudiant'    => 'Étudiant',
    'responsable' => 'Responsable pédagogique',
    'comptable'   => 'Comptable',
];

$erreur = $_GET['erreur'] ?? null;
$messagesErreur = [
    'champs_invalides'        => 'Merci de remplir tous les champs et de choisir un profil.',
    'identifiants_incorrects' => 'E-mail, mot de passe ou profil incorrect.',
    'compte_desactive'        => 'Ce compte est désactivé. Contactez votre administrateur.',
    'compte_en_attente'       => 'Ce compte est en attente de validation par un responsable pédagogique.',
];

$inscription = $_GET['inscription'] ?? null;
$messagesInscription = [
    'etudiant_ok' => 'Compte créé avec succès. Vous pouvez vous connecter dès maintenant.',
    'en_attente'  => 'Compte créé. Il sera activé dès qu\'un responsable pédagogique l\'aura validé.',
];

$roleSelectionne = $_GET['role'] ?? 'etudiant';
if (!array_key_exists($roleSelectionne, $rolesAutorises)) {
    $roleSelectionne = 'etudiant';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — Forma</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --cream:#f4f1ea;
    --ink:#1c2621;
    --green-900:#0f2b1d;
    --green-800:#153824;
    --green-700:#1c4a30;
    --green-600:#24603d;
    --line:#e3ded2;
    --muted:#6f7a70;
    --white:#ffffff;
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color:var(--ink);
    background:var(--cream);
  }
  .page{
    min-height:100vh;
    display:grid;
    grid-template-columns: 1.05fr 1fr;
  }
  @media (max-width: 880px){
    .page{ grid-template-columns: 1fr; }
    .panel-brand{ display:none; }
  }

  /* ---------- Panneau de marque (gauche) ---------- */
  .panel-brand{
    background: radial-gradient(120% 140% at 15% 10%, var(--green-700) 0%, var(--green-900) 55%);
    color: var(--cream);
    padding: 56px 56px 40px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }
  .brand-mark{
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size:20px;
    letter-spacing:0.01em;
  }
  .brand-copy{ max-width: 420px; margin-top: 64px; }
  .eyebrow{
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color: #cfe3d4;
    margin-bottom:22px;
  }
  .eyebrow::before{
    content:"";
    width:6px; height:6px;
    border-radius:50%;
    background:#7fd39c;
  }
  .brand-copy h1{
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-weight:500;
    font-size:clamp(28px, 3.4vw, 38px);
    line-height:1.15;
    margin:0 0 18px;
  }
  .brand-copy p{
    font-size:15px;
    line-height:1.6;
    color:#cfe3d4;
    margin:0;
  }
  .quote{
    max-width:380px;
    border-top:1px solid rgba(255,255,255,0.14);
    padding-top:22px;
    font-size:14px;
    line-height:1.6;
    color:#d7e6da;
  }
  .quote span{
    display:block;
    margin-top:10px;
    font-size:13px;
    color:#9db8a5;
  }

  /* ---------- Panneau formulaire (droite) ---------- */
  .panel-form{
    background:var(--white);
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 40px 32px;
  }
  .form-wrap{ width:100%; max-width:380px; }
  .form-wrap h2{
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-weight:500;
    font-size:26px;
    margin:0 0 6px;
  }
  .form-wrap > p.sub{
    font-size:14px;
    color:var(--muted);
    margin:0 0 28px;
    line-height:1.5;
  }

  /* Sélecteur de profil */
  .role-label{
    font-size:13px;
    font-weight:600;
    margin-bottom:10px;
    display:block;
  }
  .role-select{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:6px;
    padding:4px;
    background:var(--cream);
    border:1px solid var(--line);
    border-radius:10px;
    margin-bottom:24px;
  }
  .role-select input{
    position:absolute;
    opacity:0;
    pointer-events:none;
  }
  .role-select label{
    text-align:center;
    font-size:12.5px;
    font-weight:600;
    line-height:1.3;
    color:var(--muted);
    padding:9px 4px;
    border-radius:7px;
    cursor:pointer;
    transition: background .15s ease, color .15s ease;
  }
  .role-select input:checked + label{
    background:var(--green-900);
    color:var(--white);
  }

  form .field{ margin-bottom:16px; }
  form label.field-label{
    display:block;
    font-size:13px;
    font-weight:600;
    margin-bottom:7px;
  }
  .input-wrap{
    position:relative;
  }
  .input-wrap input{
    width:100%;
    padding:11px 14px 11px 38px;
    border:1px solid var(--line);
    border-radius:9px;
    font-size:14px;
    font-family: popins, sans-serif;
    background:var(--cream);
    color:var(--ink);
  }
  .input-wrap input:focus{
    outline:2px solid var(--green-700);
    outline-offset:1px;
    background:var(--white);
  }
  .input-wrap .icon{
    position:absolute;
    left:12px; top:50%;
    transform:translateY(-50%);
    color:var(--muted);
    display:flex;
  }
  .toggle-pass{
    position:absolute;
    right:12px; top:50%;
    transform:translateY(-50%);
    background:none; border:none;
    color:var(--muted);
    cursor:pointer;
    display:flex;
    padding:2px;
  }

  .row-between{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:13px;
    margin:2px 0 20px;
  }
  .remember{
    display:flex; align-items:center; gap:7px;
    color:var(--muted);
  }
  .remember input{ accent-color: var(--green-800); }
  .row-between a{
    color:var(--green-800);
    text-decoration:none;
    font-weight:600;
  }
  .row-between a:hover{ text-decoration:underline; }

  .btn-primary{
    width:100%;
    padding:12px;
    background:var(--green-800);
    color:var(--white);
    border:none;
    border-radius:9px;
    font-size:14.5px;
    font-weight:600;
    cursor:pointer;
    transition: background .15s ease;
  }
  .btn-primary:hover{ background:var(--green-700); }

  .error-box{
    background:#fbeceb;
    border:1px solid #f2c6c3;
    color:#7a2a24;
    font-size:13px;
    padding:10px 12px;
    border-radius:8px;
    margin-bottom:18px;
    line-height:1.4;
  }
  .success-box{
    background:#eaf5ee;
    border:1px solid #bfe0cc;
    color:#1c4a30;
    font-size:13px;
    padding:10px 12px;
    border-radius:8px;
    margin-bottom:18px;
    line-height:1.4;
  }

  .divider{
    display:flex; align-items:center; gap:12px;
    margin:22px 0;
    color:var(--muted);
    font-size:12.5px;
  }
  .divider::before, .divider::after{
    content:"";
    flex:1;
    height:1px;
    background:var(--line);
  }

  .btn-google{
    width:100%;
    display:flex; 
    align-items:center; 
    justify-content:center; 
    gap:10px;
    padding:11px;
    border:1px solid var(--line);
    border-radius:9px;
    background:var(--white);
    font-size:14px;
    font-weight:600;
    color:var(--ink);
    cursor:pointer;
  }
  .btn-google:hover{ background:var(--cream); }

  .signup-hint{
    text-align:center;
    font-size:13.5px;
    color:var(--muted);
    margin-top:22px;
  }
  .signup-hint a{
    color:var(--green-800);
    font-weight:600;
    text-decoration:none;
  }
  .footer-links{
    text-align:center;
    font-size:12px;
    color:#a3ab9f;
    margin-top:28px;
  }
  .footer-links a{ 
    color:inherit;
   text-decoration:none; 
  }
  .footer-links a:hover{
     text-decoration:underline;
      }
</style>
</head>
<body>
<div class="page">

  <!-- Panneau de marque -->
  <aside class="panel-brand">
    <div class="brand-mark">Geston de Centre de Formation</div>

    <div class="brand-copy">
      <span class="eyebrow">Centre de formation</span>
      <h1>Retrouvez vos formations, là où vous les avez laissées.</h1>
      <p>Un espace calme pour coordonner vos équipes, accompagner vos apprenants et garder le cap.</p>
    </div>

    <div class="quote">
      « Forma nous fait gagner du temps chaque semaine, tout en nous donnant une vision claire à toute échelle. »
      <span>Une responsable pédagogique, centre partenaire</span>
    </div>
  </aside>

  <!-- Panneau formulaire -->
  <main class="panel-form">
    <div class="form-wrap">
      <h2>Bon retour parmi nous</h2>
      <p class="sub">Connectez-vous pour accéder à votre espace Forma.</p>

      <?php if ($erreur && isset($messagesErreur[$erreur])): ?>
        <div class="error-box"><?= htmlspecialchars($messagesErreur[$erreur]) ?></div>
      <?php endif; ?>

      <?php if ($inscription && isset($messagesInscription[$inscription])): ?>
        <div class="success-box"><?= htmlspecialchars($messagesInscription[$inscription]) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= Router::url('AuthController', 'login') ?>" autocomplete="off">

        <span class="role-label">Vous êtes</span>
        <div class="role-select">
          <?php foreach ($rolesAutorises as $valeur => $libelle): ?>
            <input
              type="radio"
              name="role"
              id="role-<?= $valeur ?>"
              value="<?= $valeur ?>"
              <?= $valeur === $roleSelectionne ? 'checked' : '' ?>
            >
            <label for="role-<?= $valeur ?>"><?= htmlspecialchars($libelle) ?></label>
          <?php endforeach; ?>
        </div>

        <div class="field">
          <label class="field-label" for="email">Adresse e-mail</label>
          <div class="input-wrap">
            <span class="icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z" stroke="none"/><path d="M4 6.5 12 13l8-6.5M4 6h16v12H4z"/></svg>
            </span>
            <input type="email" id="email" name="email" placeholder="vous@centre-formation.sn" required>
          </div>
        </div>

        <div class="field">
          <label class="field-label" for="password">Mot de passe</label>
          <div class="input-wrap">
            <span class="icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            </span>
            <input type="password" id="password" name="password" placeholder="••••••••••" required>
            <button type="button" class="toggle-pass" id="togglePass" aria-label="Afficher le mot de passe">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" name="se_souvenir">
            Se souvenir de moi
          </label>
          <a href="#">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-primary">Se connecter</button>
      </form>

      <div class="divider">ou continuez avec</div>
      <button type="button" class="btn-google">
        <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.5 12.27c0-.79-.07-1.54-.2-2.27H12v4.3h6.47a5.54 5.54 0 0 1-2.4 3.63v3h3.89c2.28-2.1 3.54-5.2 3.54-8.66Z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.91l-3.89-3c-1.08.72-2.46 1.15-4.04 1.15-3.1 0-5.73-2.09-6.67-4.9H1.3v3.09A12 12 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.33 14.34a7.2 7.2 0 0 1 0-4.68V6.57H1.3a12 12 0 0 0 0 10.86l4.03-3.09Z"/><path fill="#EA4335" d="M12 4.77c1.76 0 3.34.6 4.58 1.79l3.44-3.44C17.94 1.19 15.23 0 12 0A12 12 0 0 0 1.3 6.57l4.03 3.09C6.27 6.86 8.9 4.77 12 4.77Z"/></svg>
        Continuer avec Google
      </button>

      <p class="signup-hint">
        Pas encore de compte ? <a href="<?= BASE_URL ?>views/auth/register.php">Créer un compte</a>
      </p>

      <div class="footer-links">
        <a href="#">Confidentialité</a> · <a href="#">Conditions d'utilisation</a>
      </div>
    </div>
  </main>

</div>

<script>
  document.getElementById('togglePass').addEventListener('click', function () {
    const input = document.getElementById('password');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
  });
</script>
</body>
</html>
>>>>>>> 817486c1d7c15f130a8ca5759f13afe0a570c390
