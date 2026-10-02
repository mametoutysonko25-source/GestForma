<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$requete = $db->prepare(
    "SELECT e.idEvaluation, e.titre, e.description, e.dateOuverture, e.dateLimite,
            m.libelle AS module, dpt.idDepot, dpt.fichier, dpt.dateDepot
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN semestre se ON se.idNiveau = i.idNiveau
     INNER JOIN module m ON m.idSemestre = se.idSemestre
     INNER JOIN evaluation e ON e.idModule = m.idModule
     LEFT JOIN depot dpt ON dpt.idEvaluation = e.idEvaluation AND dpt.idEtudiant = :idEtudiantDepot
     WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
       AND i.idInscription = (
           SELECT MAX(i2.idInscription)
           FROM inscription i2
           INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
           WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
       )
     ORDER BY e.dateLimite ASC, m.libelle"
);
$requete->execute([
    'idEtudiantDepot' => $currentUser['id'],
    'idEtudiant' => $currentUser['id'],
    'idEtudiantRecent' => $currentUser['id'],
]);
$evaluations = $requete->fetchAll();

$pageTitle = 'Mes évaluations';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Mes évaluations</h2><p>Consultez les sujets et vérifiez l’état de vos rendus.</p></div></div>
<div class="card table-card" style="overflow-x:auto;">
    <table class="director-table">
        <thead><tr><th>Module</th><th>Évaluation</th><th>Date limite</th><th>État</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($evaluations as $evaluation): ?>
            <?php $dateLimite = strtotime($evaluation['dateLimite']); $estExpiree = $dateLimite < time() && !$evaluation['idDepot']; ?>
            <tr>
                <td><?= htmlspecialchars($evaluation['module']) ?></td>
                <td><strong><?= htmlspecialchars($evaluation['titre']) ?></strong><?php if ($evaluation['description']): ?><br><span class="muted-label"><?= htmlspecialchars($evaluation['description']) ?></span><?php endif; ?></td>
                <td><?= htmlspecialchars(date('d/m/Y H:i', $dateLimite)) ?></td>
                <td><?= $evaluation['idDepot'] ? 'Rendue le ' . htmlspecialchars(date('d/m/Y H:i', strtotime($evaluation['dateDepot']))) : ($estExpiree ? 'Date dépassée' : 'À rendre') ?></td>
                <td><?php if ($evaluation['idDepot']): ?><a class="btn btn-small btn-secondary" href="<?= htmlspecialchars(BASE_URL . $evaluation['fichier']) ?>" target="_blank" rel="noopener">Voir mon rendu</a><?php elseif (!$estExpiree): ?><a class="btn btn-small btn-primary" href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/depot.php?evaluation=' . $evaluation['idEvaluation']) ?>">Rendre</a><?php else: ?>-<?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$evaluations): ?><tr><td colspan="5">Aucune évaluation disponible pour votre inscription.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
