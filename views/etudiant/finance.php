<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/EspaceEtudiantController.php';

$currentUser = requireRole(['etudiant']);
$finance = (new EspaceEtudiantController())->finance((int) $currentUser['id'])[0] ?? null;

$pageTitle = 'Situation financière';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Situation financière</h2>

<?php if ($finance && $finance['idInscription']): ?>
    <div class="card" style="max-width:640px;">
        <p><strong>Inscription :</strong> <?= htmlspecialchars($finance['statut']) ?></p>
        <p><strong>Total payé :</strong> <?= number_format((float) $finance['totalPaye'], 2, ',', ' ') ?> FCFA</p>
        <p><strong>Nombre de paiements :</strong> <?= (int) $finance['nombrePaiements'] ?></p>
    </div>
<?php else: ?>
    <div class="card" style="max-width:640px;">
        <p style="color:var(--muted);">Aucune inscription ou aucun paiement enregistré.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
