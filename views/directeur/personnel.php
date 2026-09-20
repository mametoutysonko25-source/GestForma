<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../models/Directeur.php';
requireRole(['directeur']);
$personnel = Directeur::personnel();
$pageTitle = 'Personnel';
$activeMenu = 'personnel';
$contentClass = 'director-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Personnel</h2><p>Gestion des membres du personnel (<?= count($personnel) ?>).</p></div><a class="btn" href="#personnel">+ Ajouter</a></div>
<div class="card director-panel" style="overflow-x:auto;"><table class="director-table"><thead><tr><th>Nom complet</th><th>Rôle</th><th>E-mail</th><th>Téléphone</th><th>Statut</th></tr></thead><tbody><?php foreach ($personnel as $membre): ?><tr><td><strong><?= htmlspecialchars(trim($membre['prenom'] . ' ' . $membre['nom'])) ?></strong></td><td><?= htmlspecialchars($membre['role']) ?></td><td><?= htmlspecialchars($membre['email']) ?></td><td><?= htmlspecialchars($membre['telephone'] ?: '-') ?></td><td><?= htmlspecialchars($membre['statutCompte']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>