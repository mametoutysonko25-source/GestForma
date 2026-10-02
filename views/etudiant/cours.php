<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$requete = $db->prepare(
    "SELECT DISTINCT m.idModule, f.libelle AS formation, n.libelle AS niveau,
            se.libelle AS semestre, m.libelle AS module, m.description,
            s.titre AS support, s.type, s.fichier, s.dateAjout
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     INNER JOIN formation f ON f.idFormation = n.idFormation
     INNER JOIN semestre se ON se.idNiveau = n.idNiveau
     INNER JOIN module m ON m.idSemestre = se.idSemestre
     LEFT JOIN support s ON s.idModule = m.idModule
     WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
       AND i.idInscription = (
           SELECT MAX(i2.idInscription)
           FROM inscription i2
           INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
           WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
       )
     ORDER BY se.ordre, m.libelle, s.dateAjout DESC"
);
$requete->execute(['idEtudiant' => $currentUser['id'], 'idEtudiantRecent' => $currentUser['id']]);
$cours = $requete->fetchAll();

$pageTitle = 'Voir mes cours';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Voir mes cours</h2><p>Vos modules et les documents déposés par vos formateurs.</p></div></div>
<div class="card table-card" style="overflow-x:auto;">
    <table class="director-table">
        <thead><tr><th>Module</th><th>Formation / niveau</th><th>Document</th><th>Type</th><th>Accès</th></tr></thead>
        <tbody>
        <?php foreach ($cours as $cour): ?>
            <tr>
                <td><strong><?= htmlspecialchars($cour['module']) ?></strong><?php if ($cour['description']): ?><br><span class="muted-label"><?= htmlspecialchars($cour['description']) ?></span><?php endif; ?></td>
                <td><?= htmlspecialchars($cour['formation'] . ' - ' . $cour['niveau'] . ' / ' . $cour['semestre']) ?></td>
                <td><?= htmlspecialchars($cour['support'] ?: 'Aucun document') ?></td>
                <td><?= htmlspecialchars($cour['type'] ?: '-') ?></td>
                <td><?php if ($cour['fichier']): ?><a class="btn btn-small btn-secondary" href="<?= htmlspecialchars(BASE_URL . $cour['fichier']) ?>" target="_blank" rel="noopener">Ouvrir</a><?php else: ?>-<?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$cours): ?><tr><td colspan="5">Aucun cours disponible. Les cours apparaîtront après validation de votre inscription.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
