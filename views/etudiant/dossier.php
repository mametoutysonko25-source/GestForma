<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/EspaceEtudiantController.php';

$currentUser = requireRole(['etudiant']);
$dossier = (new EspaceEtudiantController())->dossier((int) $currentUser['id']);

$pageTitle = 'Mon dossier';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Mon dossier</h2>

<?php if ($dossier): ?>
    <div class="card" style="max-width:640px;">
        <p><strong>Nom :</strong> <?= htmlspecialchars($dossier['prenom'] . ' ' . $dossier['nom']) ?></p>
        <p><strong>E-mail :</strong> <?= htmlspecialchars($dossier['email']) ?></p>
        <p><strong>Matricule :</strong> <?= htmlspecialchars($dossier['matricule']) ?></p>
        <?php if ($dossier['dateNaissance']): ?>
            <p><strong>Date de naissance :</strong> <?= htmlspecialchars($dossier['dateNaissance']) ?></p>
        <?php endif; ?>
        <?php if ($dossier['adresse']): ?>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($dossier['adresse']) ?></p>
        <?php endif; ?>
        <?php if ($dossier['anneeScolaire']): ?>
            <p><strong>Annee scolaire :</strong> <?= htmlspecialchars($dossier['anneeScolaire']) ?></p>
            <p><strong>Statut du dossier :</strong> <?= htmlspecialchars($dossier['statutDossier']) ?></p>
        <?php else: ?>
            <p style="color:var(--muted);">Aucun dossier de formation n'est encore cree.</p>
        <?php endif; ?>
        <?php if ($dossier['statutInscription']): ?>
            <p><strong>Statut de l'inscription :</strong> <?= htmlspecialchars($dossier['statutInscription']) ?></p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card" style="max-width:640px;">
        <p style="color:var(--muted);">Les informations de votre dossier sont indisponibles.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/footer.php'; ?>