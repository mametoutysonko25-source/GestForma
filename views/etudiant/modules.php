<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$requete = $db->prepare(
    "SELECT DISTINCT f.libelle AS formation, n.libelle AS niveau,
            se.libelle AS semestre, m.libelle AS module,
            m.description, m.volumeHoraire
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     INNER JOIN formation f ON f.idFormation = n.idFormation
     INNER JOIN semestre se ON se.idNiveau = n.idNiveau
     INNER JOIN module m ON m.idSemestre = se.idSemestre
     WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
       AND i.idInscription = (
           SELECT MAX(i2.idInscription)
           FROM inscription i2
           INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
           WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
       )
     ORDER BY se.ordre, m.libelle"
);
$requete->execute(['idEtudiant' => $currentUser['id'], 'idEtudiantRecent' => $currentUser['id']]);
$modules = $requete->fetchAll();

$pageTitle = 'Mes modules';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>

<div class="director-heading"><div><h2>Mes modules</h2><p><?= count($modules) ?> module(s) pour votre dernière inscription validée.</p></div></div>
<div class="card table-card" style="overflow-x:auto;">
    <table class="director-table">
        <thead><tr><th>Formation</th><th>Niveau</th><th>Semestre</th><th>Module</th><th>Volume horaire</th></tr></thead>
        <tbody>
        <?php foreach ($modules as $module): ?>
            <tr>
                <td><?= htmlspecialchars($module['formation']) ?></td>
                <td><?= htmlspecialchars($module['niveau']) ?></td>
                <td><?= htmlspecialchars($module['semestre']) ?></td>
                <td><strong><?= htmlspecialchars($module['module']) ?></strong><?php if ($module['description']): ?><br><span class="muted-label"><?= htmlspecialchars($module['description']) ?></span><?php endif; ?></td>
                <td><?= $module['volumeHoraire'] !== null ? (int) $module['volumeHoraire'] . ' h' : '-' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$modules): ?><tr><td colspan="5">Aucun module disponible. Les modules apparaîtront après validation de votre inscription.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>