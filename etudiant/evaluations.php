<?php
require_once __DIR__ . '/../controllers/helpers.php'; require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $evaluations = (new EspaceEtudiantController())->evaluations((int) $user['id']);
$pageTitle = 'Évaluations'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h1>Mes évaluations</h1><div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Évaluation</th><th>Module</th><th>Échéance</th><th>État du dépôt</th></tr></thead><tbody>
<?php foreach ($evaluations as $evaluation): ?><tr><td><?= htmlspecialchars($evaluation['titre']) ?></td><td><?= htmlspecialchars($evaluation['module']) ?></td><td><?= htmlspecialchars($evaluation['dateLimite']) ?></td><td><?= $evaluation['idDepot'] ? 'Déposé le ' . htmlspecialchars($evaluation['dateDepot']) : '<a href="depot.php?idEvaluation=' . (int) $evaluation['idEvaluation'] . '">Déposer mon travail</a>' ?></td></tr><?php endforeach; ?>
<?php if (!$evaluations): ?><tr><td colspan="4">Aucune évaluation disponible.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>