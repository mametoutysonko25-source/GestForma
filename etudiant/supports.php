<?php
require_once __DIR__ . '/../controllers/helpers.php'; require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $supports = (new EspaceEtudiantController())->supports((int) $user['id']);
$pageTitle = 'Supports de cours'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h1>Supports de cours</h1><div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Titre</th><th>Module</th><th>Type</th><th>Ajouté le</th><th>Fichier</th></tr></thead><tbody>
<?php foreach ($supports as $support): ?><tr><td><?= htmlspecialchars($support['titre']) ?></td><td><?= htmlspecialchars($support['module']) ?></td><td><?= htmlspecialchars($support['type']) ?></td><td><?= htmlspecialchars($support['dateAjout']) ?></td><td><a href="<?= htmlspecialchars($support['fichier']) ?>" target="_blank" rel="noopener">Ouvrir</a></td></tr><?php endforeach; ?>
<?php if (!$supports): ?><tr><td colspan="5">Aucun support disponible.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>