<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$requete = $db->prepare(
    "SELECT r.note, r.appreciation, r.datePublication,
            ev.titre AS evaluation, m.libelle AS module
     FROM resultat r
     INNER JOIN evaluation ev ON ev.idEvaluation = r.idEvaluation
     INNER JOIN module m ON m.idModule = ev.idModule
     WHERE r.idEtudiant = :idEtudiant AND r.datePublication IS NOT NULL
     ORDER BY r.datePublication DESC, m.libelle, ev.titre"
);
$requete->execute(['idEtudiant' => $currentUser['id']]);
$resultats = $requete->fetchAll();

$pageTitle = 'Mes résultats';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>

<div class="director-heading"><div><h2>Mes résultats</h2><p>Notes et appréciations publiées par vos formateurs.</p></div></div>
<div class="card table-card" style="overflow-x:auto;">
    <table class="director-table">
        <thead><tr><th>Module</th><th>Évaluation</th><th>Note</th><th>Appréciation</th><th>Publication</th></tr></thead>
        <tbody>
        <?php foreach ($resultats as $resultat): ?>
            <tr>
                <td><?= htmlspecialchars($resultat['module']) ?></td>
                <td><strong><?= htmlspecialchars($resultat['evaluation']) ?></strong></td>
                <td><?= $resultat['note'] !== null ? number_format((float) $resultat['note'], 2, ',', ' ') . ' / 20' : 'Non noté' ?></td>
                <td><?= htmlspecialchars($resultat['appreciation'] ?: '-') ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($resultat['datePublication']))) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$resultats): ?><tr><td colspan="5">Aucun résultat publié pour le moment.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>