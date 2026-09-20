<?php
require_once __DIR__ . '/../controllers/helpers.php';
require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']);
$planning = (new EspaceEtudiantController())->planning((int) $user['id']);
$pageTitle = 'Emploi du temps'; $showSidebar = true;
require __DIR__ . '/../includes/header.php';
?>
<h1>Emploi du temps</h1>
<div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Date</th><th>Horaire</th><th>Module</th><th>Formateur</th><th>Statut</th></tr></thead><tbody>
<?php foreach ($planning as $seance): ?><tr><td><?= htmlspecialchars($seance['dateSeance']) ?></td><td><?= htmlspecialchars(substr($seance['heureDebut'], 0, 5) . ' - ' . substr($seance['heureFin'], 0, 5)) ?></td><td><?= htmlspecialchars($seance['module']) ?></td><td><?= htmlspecialchars($seance['prenom'] . ' ' . $seance['nom']) ?></td><td><?= htmlspecialchars($seance['statut']) ?></td></tr><?php endforeach; ?>
<?php if (!$planning): ?><tr><td colspan="5">Aucune séance programmée.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>