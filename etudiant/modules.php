<?php
require_once __DIR__ . '/../controllers/helpers.php'; require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $modules = (new EspaceEtudiantController())->modules((int) $user['id']);
$pageTitle = 'Mes modules'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h1>Mes modules</h1><div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Module</th><th>Semestre</th><th>Volume horaire</th><th>Description</th></tr></thead><tbody>
<?php foreach ($modules as $module): ?><tr><td><?= htmlspecialchars($module['libelle']) ?></td><td><?= htmlspecialchars($module['semestre']) ?></td><td><?= htmlspecialchars((string) $module['volumeHoraire']) ?> h</td><td><?= htmlspecialchars((string) $module['description']) ?></td></tr><?php endforeach; ?>
<?php if (!$modules): ?><tr><td colspan="4">Aucun module pour une inscription validée.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>