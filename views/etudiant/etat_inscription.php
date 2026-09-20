<?php
require_once __DIR__ . '/../../controllers/helpers.php'; require_once __DIR__ . '/../../controllers/InscriptionController.php';
$user = requireRole(['etudiant']); $inscription = (new InscriptionController())->getEtatInscription((int) $user['id']); $pageTitle = 'État de l’inscription'; $showSidebar = true; require __DIR__ . '/../../includes/header.php';
?>
<h1>État de mon inscription</h1><div class="card"><?php if ($inscription): ?><p><strong>Statut :</strong> <?= htmlspecialchars($inscription['statut']) ?></p><p><strong>Date :</strong> <?= htmlspecialchars($inscription['dateInscription']) ?></p><p><strong>Niveau :</strong> <?= htmlspecialchars((string) ($inscription['niveau_libelle'] ?? '')) ?></p><?php else: ?>Aucune demande d’inscription trouvée.<?php endif; ?></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>