<?php
require_once __DIR__ . '/../controllers/helpers.php'; require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $resultats = (new EspaceEtudiantController())->resultats((int) $user['id']);
$pageTitle = 'Résultats'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h1>Mes résultats</h1><div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Évaluation</th><th>Module</th><th>Note</th><th>Appréciation</th><th>Publication</th></tr></thead><tbody>
<?php foreach ($resultats as $resultat): ?><tr><td><?= htmlspecialchars($resultat['titre']) ?></td><td><?= htmlspecialchars($resultat['module']) ?></td><td><?= htmlspecialchars((string) $resultat['note']) ?>/20</td><td><?= htmlspecialchars((string) $resultat['appreciation']) ?></td><td><?= htmlspecialchars((string) $resultat['datePublication']) ?></td></tr><?php endforeach; ?>
<?php if (!$resultats): ?><tr><td colspan="5">Aucun résultat publié.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>