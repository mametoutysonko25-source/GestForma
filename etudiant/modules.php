<?php
require_once __DIR__ . '/../controllers/helpers.php';
require_once __DIR__ . '/../config/database.php';
$user = requireRole(['etudiant']);
$stmt = database()->prepare('SELECT DISTINCT m.idModule, m.libelle, m.description, m.volumeHoraire, sm.libelle AS semestre FROM module m INNER JOIN semestre sm ON sm.idSemestre = m.idSemestre INNER JOIN niveau n ON n.idNiveau = sm.idNiveau INNER JOIN inscription i ON i.idNiveau = n.idNiveau INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier WHERE d.idEtudiant = :id AND i.statut = "VALIDEE" ORDER BY sm.ordre, m.libelle');
$stmt->execute(['id' => $user['id']]);
$modules = $stmt->fetchAll();
$pageTitle = 'Mes modules'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h2 style="margin-top:0;">Mes modules</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
<?php foreach ($modules as $module): ?><div class="card"><strong><?= htmlspecialchars('Module '.$module['idModule'].' - '.$module['libelle']) ?></strong><p><?= htmlspecialchars($module['description'] ?? 'Aucune description.') ?></p><small><?= htmlspecialchars($module['semestre']) ?> · <?= htmlspecialchars((string) $module['volumeHoraire']) ?> heures</small></div><?php endforeach; ?>
<?php if (!$modules): ?><p>Aucun module associé à votre inscription.</p><?php endif; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>