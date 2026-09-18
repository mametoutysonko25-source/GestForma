<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/Router.php';

// Rôles ouverts à l'inscription publique
$rolesAutorises = [
    'etudiant'    => 'Étudiant',
    'responsable' => 'Responsable pédagogique',
    'comptable'   => 'Comptable',
];

$roleSelectionne = $_GET['role'] ?? 'etudiant';
if (!array_key_exists($roleSelectionne, $rolesAutorises)) {
    $roleSelectionne = 'etudiant';
}

$erreur = $_GET['erreur'] ?? null;
$messagesErreur = [
    'role_invalide'            => 'Merci de choisir un profil valide.',
    'champs_invalides'         => 'Merci de remplir tous les champs obligatoires.',
    'email_invalide'           => "Cette adresse e-mail n'est pas valide.",
    'mots_de_passe_differents' => 'Les deux mots de passe ne correspondent pas.',
    'deja_utilise'             => 'Cet e-mail ou ce nom d\'utilisateur est déjà utilisé.',
    'technique'                => "Une erreur est survenue, merci de réessayer.",
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer un compte — Forma</title>
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
    --line:#e3ded2;
    --muted:#6f7a70;
    --white:#ffffff;
    --amber-bg:#fbf3e2;
    --amber-line:#eddcb0;
    --amber-ink:#6b4f14;
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    font-family:'Inter',system-ui,sans-serif;
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

  .panel-brand{
    background: radial-gradient(120% 140% at 15% 10%, var(--green-700) 0%, var(--green-900) 55%);
    color: var(--cream);
    padding: 56px 56px 40px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }
  .brand-mark{ font-family:'Fraunces', serif; font-size:20px; letter-spacing:0.01em; }
  .brand-copy{ max-width: 420px; margin-top: 64px; }
  .eyebrow{
    display:inline-flex; align-items:center; gap:8px;
    font-size:13px; color:#cfe3d4; margin-bottom:22px;
  }
  .eyebrow::before{ content:""; width:6px; height:6px; border-radius:50%; background:#7fd39c; }
  .brand-copy h1{
    font-family:'Fraunces', serif; font-weight:500;
    font-size:clamp(28px, 3.4vw, 38px); line-height:1.15; margin:0 0 18px;
  }
  .brand-copy p{ font-size:15px; line-height:1.6; color:#cfe3d4; margin:0; }
  .quote{
    max-width:380px; border-top:1px solid rgba(255,255,255,0.14);
    padding-top:22px; font-size:14px; line-height:1.6; color:#d7e6da;
  }
  .quote span{ display:block; margin-top:10px; font-size:13px; color:#9db8a5; }

  .panel-form{
    background:var(--white);
    display:flex; align-items:center; justify-content:center;
    padding: 40px 32px;
  }
  .form-wrap{ width:100%; max-width:420px; }
  .form-wrap h2{
    font-family:'Fraunces', serif; font-weight:500; font-size:26px; margin:0 0 6px;
  }
  .form-wrap > p.sub{ font-size:14px; color:var(--muted); margin:0 0 24px; line-height:1.5; }

  .role-label{ font-size:13px; font-weight:600; margin-bottom:10px; display:block; }
  .role-select{
    display:grid; grid-template-columns: repeat(3, 1fr); gap:6px;
    padding:4px; background:var(--cream); border:1px solid var(--line);
    border-radius:10px; margin-bottom:10px;
  }
  .role-select input{ position:absolute; opacity:0; pointer-events:none; }
  .role-select label{
    text-align:center; font-size:12.5px; font-weight:600; line-height:1.3;
    color:var(--muted); padding:9px 4px; border-radius:7px; cursor:pointer;
    transition: background .15s ease, color .15s ease;
  }
  .role-select input:checked + label{ background:var(--green-900); color:var(--white); }

  .role-note{
    display:none;
    font-size:12.5px; line-height:1.5;
    background:var(--amber-bg); border:1px solid var(--amber-line); color:var(--amber-ink);
    padding:9px 11px; border-radius:8px; margin-bottom:20px;
  }
  .role-note.visible{ display:block; }

  form .field{ margin-bottom:14px; }
  form .field-row{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  form label.field-label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; }
  form input, form select{
    width:100%; padding:10px 12px; border:1px solid var(--line);
    border-radius:9px; font-size:14px; font-family:inherit;
    background:var(--cream); color:var(--ink);
  }
  form input:focus, form select:focus{
    outline:2px solid var(--green-700); outline-offset:1px; background:var(--white);
  }

  .role-fields{ display:none; }
  .role-fields.visible{ display:block; }

  .btn-primary{
    width:100%; padding:12px; margin-top:6px;
    background:var(--green-800); color:var(--white);
    border:none; border-radius:9px; font-size:14.5px; font-weight:600; cursor:pointer;
    transition: background .15s ease;
  }
  .btn-primary:hover{ background:var(--green-700); }

  .error-box{
    background:#fbeceb; border:1px solid #f2c6c3; color:#7a2a24;
    font-size:13px; padding:10px 12px; border-radius:8px; margin-bottom:18px; line-height:1.4;
  }

  .signin-hint{ text-align:center; font-size:13.5px; color:var(--muted); margin-top:20px; }
  .signin-hint a{ color:var(--green-800); font-weight:600; text-decoration:none; }
</style>
</head>
<body>
<div class="page">

  <aside class="panel-brand">
    <div class="brand-mark">forma.</div>
    <div class="brand-copy">
      <span class="eyebrow">Votre centre, simplement</span>
      <h1>Rejoignez l'espace qui garde toute l'équipe alignée.</h1>
      <p>Étudiants, responsables pédagogiques et comptables retrouvent chacun leur espace, au même endroit.</p>
    </div>
    <div class="quote">
      « L'inscription en ligne nous fait gagner un temps précieux à chaque rentrée. »
      <span>Une responsable pédagogique, centre partenaire</span>
    </div>
  </aside>

  <main class="panel-form">
    <div class="form-wrap">
      <h2>Créer un compte</h2>
      <p class="sub">Choisissez votre profil pour commencer.</p>

      <?php if ($erreur && isset($messagesErreur[$erreur])): ?>
        <div class="error-box"><?= htmlspecialchars($messagesErreur[$erreur]) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= Router::url('AuthController', 'register') ?>" autocomplete="off">

        <span class="role-label">Vous êtes</span>
        <div class="role-select">
          <?php foreach ($rolesAutorises as $valeur => $libelle): ?>
            <input
              type="radio"
              name="role"
              id="role-<?= $valeur ?>"
              value="<?= $valeur ?>"
              data-role-input
              <?= $valeur === $roleSelectionne ? 'checked' : '' ?>
            >
            <label for="role-<?= $valeur ?>"><?= htmlspecialchars($libelle) ?></label>
          <?php endforeach; ?>
        </div>

        <p class="role-note" data-role-note="responsable">
          Ce profil est soumis à validation : un responsable pédagogique déjà en poste doit approuver votre compte avant que vous puissiez vous connecter.
        </p>
        <p class="role-note" data-role-note="comptable">
          Ce profil est soumis à validation : un responsable pédagogique doit approuver votre compte avant que vous puissiez vous connecter.
        </p>

        <div class="field-row">
          <div class="field">
            <label class="field-label" for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>
          </div>
          <div class="field">
            <label class="field-label" for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>
          </div>
        </div>

        <div class="field">
          <label class="field-label" for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" placeholder="vous@centre-formation.sn" required>
        </div>

        <div class="field-row">
          <div class="field">
            <label class="field-label" for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone">
          </div>
          <div class="field">
            <label class="field-label" for="nomUtilisateur">Nom d'utilisateur</label>
            <input type="text" id="nomUtilisateur" name="nomUtilisateur" required>
          </div>
        </div>

        <div class="field-row">
          <div class="field">
            <label class="field-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="field">
            <label class="field-label" for="password2">Confirmer</label>
            <input type="password" id="password2" name="password2" required>
          </div>
        </div>

        <!-- Champs spécifiques Étudiant -->
        <div class="role-fields" data-role-fields="etudiant">
          <div class="field-row">
            <div class="field">
              <label class="field-label" for="dateNaissance">Date de naissance</label>
              <input type="date" id="dateNaissance" name="dateNaissance">
            </div>
            <div class="field">
              <label class="field-label" for="lieuNaissance">Lieu de naissance</label>
              <input type="text" id="lieuNaissance" name="lieuNaissance">
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="sexe">Sexe</label>
            <select id="sexe" name="sexe">
              <option value="">— Choisir —</option>
              <option value="M">Masculin</option>
              <option value="F">Féminin</option>
            </select>
          </div>
        </div>

        <!-- Champs spécifiques Responsable pédagogique -->
        <div class="role-fields" data-role-fields="responsable">
          <div class="field">
            <label class="field-label" for="specialite">Spécialité</label>
            <input type="text" id="specialite" name="specialite" placeholder="Ex. Pédagogie, Informatique...">
          </div>
        </div>

        <button type="submit" class="btn-primary">Créer mon compte</button>
      </form>

      <p class="signin-hint">
        Déjà un compte ? <a href="<?= BASE_URL ?>views/auth/login.php">Se connecter</a>
      </p>
    </div>
  </main>

</div>

<script>
  const roleInputs  = document.querySelectorAll('[data-role-input]');
  const roleFields  = document.querySelectorAll('[data-role-fields]');
  const roleNotes   = document.querySelectorAll('[data-role-note]');

  function appliquerRole(role) {
    roleFields.forEach(el => el.classList.toggle('visible', el.dataset.roleFields === role));
    roleNotes.forEach(el => el.classList.toggle('visible', el.dataset.roleNote === role));
  }

  roleInputs.forEach(input => {
    input.addEventListener('change', () => appliquerRole(input.value));
  });

  const initial = document.querySelector('[data-role-input]:checked');
  if (initial) appliquerRole(initial.value);
</script>
</body>
</html>
