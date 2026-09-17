<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/Router.php';
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../models/User.php';

$utilisateur = requireRole(['responsable']);
$comptes = User::comptesEnAttente();

$statut = $_GET['statut'] ?? null;
$messagesStatut = [
    'valide'  => 'Compte validé et activé.',
    'modifie' => 'Informations mises à jour.',
    'refuse'  => 'Demande refusée et supprimée.',
];

$libellesRole = [
    'responsable' => 'Responsable pédagogique',
    'comptable'   => 'Comptable',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comptes en attente — Forma</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --cream:#f4f1ea; --ink:#1c2621;
    --green-900:#0f2b1d; --green-800:#153824; --green-700:#1c4a30;
    --line:#e3ded2; --muted:#6f7a70; --white:#ffffff;
    --red:#b3413a; --red-bg:#fbeceb; --red-line:#f2c6c3;
  }
  *{box-sizing:border-box;}
  body{ margin:0; font-family:'Inter',system-ui,sans-serif; color:var(--ink); background:var(--cream); }

  .topbar{
    display:flex; align-items:center; justify-content:space-between;
    padding:18px 32px; background:var(--white); border-bottom:1px solid var(--line);
  }
  .brand-mark{ font-family:'Fraunces', serif; font-size:19px; color:var(--green-900); }
  .who{ font-size:13px; color:var(--muted); }
  .who strong{ color:var(--ink); }

  .wrap{ max-width:960px; margin:0 auto; padding:36px 24px 60px; }
  .wrap h1{ font-family:'Fraunces', serif; font-weight:500; font-size:26px; margin:0 0 6px; }
  .wrap > p.sub{ font-size:14px; color:var(--muted); margin:0 0 26px; }

  .status-box{
    background:#eaf5ee; border:1px solid #bfe0cc; color:#1c4a30;
    font-size:13px; padding:10px 14px; border-radius:8px; margin-bottom:22px;
  }

  .empty{
    background:var(--white); border:1px solid var(--line); border-radius:12px;
    padding:40px 24px; text-align:center; color:var(--muted); font-size:14px;
  }

  .card{
    background:var(--white); border:1px solid var(--line); border-radius:12px;
    padding:18px 20px; margin-bottom:14px;
  }
  .card-top{
    display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;
  }
  .identite{ font-size:15px; font-weight:600; }
  .identite .role-pill{
    display:inline-block; margin-left:8px; font-size:11.5px; font-weight:600;
    color:var(--green-800); background:#e7f1ea; padding:3px 8px; border-radius:999px;
  }
  .meta{ font-size:13px; color:var(--muted); margin-top:4px; line-height:1.6; }

  .actions{ display:flex; gap:8px; flex-wrap:wrap; }
  .btn{
    border:none; border-radius:8px; font-size:13px; font-weight:600;
    padding:8px 14px; cursor:pointer; font-family:inherit;
  }
  .btn-valider{ background:var(--green-800); color:var(--white); }
  .btn-valider:hover{ background:var(--green-700); }
  .btn-modifier{ background:var(--cream); color:var(--ink); border:1px solid var(--line); }
  .btn-modifier:hover{ background:#ece7da; }
  .btn-refuser{ background:var(--red-bg); color:var(--red); border:1px solid var(--red-line); }
  .btn-refuser:hover{ background:#f6d9d6; }

  .edit-form{
    display:none; margin-top:14px; padding-top:14px; border-top:1px solid var(--line);
    gap:10px; grid-template-columns:1fr 1fr auto;
  }
  .edit-form.visible{ display:grid; }
  .edit-form label{ font-size:12px; font-weight:600; display:block; margin-bottom:5px; }
  .edit-form input{
    width:100%; padding:9px 11px; border:1px solid var(--line); border-radius:8px;
    font-size:13.5px; font-family:inherit; background:var(--cream);
  }
  .edit-form .btn-enregistrer{
    align-self:end; background:var(--green-800); color:var(--white);
  }

  form.inline{ display:inline; }
</style>
</head>
<body>

<div class="topbar">
  <div class="brand-mark">forma.</div>
  <div class="who">Connecté en tant que <strong><?= htmlspecialchars($utilisateur['nom']) ?></strong> — Responsable pédagogique</div>
</div>

<div class="wrap">
  <h1>Comptes en attente de validation</h1>
  <p class="sub">Approuvez, modifiez ou refusez les demandes d'inscription des responsables pédagogiques et comptables.</p>

  <?php if ($statut && isset($messagesStatut[$statut])): ?>
    <div class="status-box"><?= htmlspecialchars($messagesStatut[$statut]) ?></div>
  <?php endif; ?>

  <?php if (empty($comptes)): ?>
    <div class="empty">Aucune demande en attente pour le moment.</div>
  <?php else: ?>
    <?php foreach ($comptes as $compte): ?>
      <div class="card">
        <div class="card-top">
          <div>
            <div class="identite">
              <?= htmlspecialchars($compte['prenom'] . ' ' . $compte['nom']) ?>
              <span class="role-pill"><?= htmlspecialchars($libellesRole[$compte['role']]) ?></span>
            </div>
            <div class="meta">
              <?= htmlspecialchars($compte['email']) ?><?= $compte['telephone'] ? ' · ' . htmlspecialchars($compte['telephone']) : '' ?><br>
              <?php if ($compte['role'] === 'responsable'): ?>
                Matricule : <?= htmlspecialchars($compte['matricule']) ?> · Spécialité : <?= htmlspecialchars($compte['specialite'] ?: '—') ?><br>
              <?php endif; ?>
              Demande envoyée le <?= htmlspecialchars(date('d/m/Y', strtotime($compte['dateCreation']))) ?>
            </div>
          </div>

          <div class="actions">
            <form class="inline" method="post" action="<?= Router::url('ResponsableController', 'valider') ?>">
              <input type="hidden" name="idUtilisateur" value="<?= (int) $compte['idUtilisateur'] ?>">
              <button type="submit" class="btn btn-valider">Valider (activer)</button>
            </form>

            <?php if ($compte['role'] === 'responsable'): ?>
              <button type="button" class="btn btn-modifier" data-toggle-edit="edit-<?= (int) $compte['idUtilisateur'] ?>">Modifier</button>
            <?php endif; ?>

            <form class="inline" method="post" action="<?= Router::url('ResponsableController', 'refuser') ?>"
                  onsubmit="return confirm('Refuser et supprimer définitivement cette demande ?');">
              <input type="hidden" name="idUtilisateur" value="<?= (int) $compte['idUtilisateur'] ?>">
              <button type="submit" class="btn btn-refuser">Refuser (supprimer)</button>
            </form>
          </div>
        </div>

        <?php if ($compte['role'] === 'responsable'): ?>
          <form class="edit-form" id="edit-<?= (int) $compte['idUtilisateur'] ?>" method="post" action="<?= Router::url('ResponsableController', 'modifier') ?>">
            <input type="hidden" name="idUtilisateur" value="<?= (int) $compte['idUtilisateur'] ?>">
            <div>
              <label>Matricule</label>
              <input type="text" name="matricule" value="<?= htmlspecialchars($compte['matricule']) ?>" required>
            </div>
            <div>
              <label>Spécialité</label>
              <input type="text" name="specialite" value="<?= htmlspecialchars($compte['specialite'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-enregistrer">Enregistrer</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
  document.querySelectorAll('[data-toggle-edit]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById(btn.dataset.toggleEdit).classList.toggle('visible');
    });
  });
</script>
</body>
</html>
