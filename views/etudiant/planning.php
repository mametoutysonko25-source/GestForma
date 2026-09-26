<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$requete = $db->prepare(
    "SELECT s.dateSeance, s.heureDebut, s.heureFin, s.statut,
            m.libelle AS module, u.prenom AS formateurPrenom, u.nom AS formateurNom
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN semestre se ON se.idNiveau = i.idNiveau
     INNER JOIN module m ON m.idSemestre = se.idSemestre
     INNER JOIN seance s ON s.idModule = m.idModule
     LEFT JOIN utilisateur u ON u.idUtilisateur = s.idFormateur
     WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
       AND i.idInscription = (
           SELECT MAX(i2.idInscription)
           FROM inscription i2
           INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
           WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
       )
     ORDER BY s.dateSeance, s.heureDebut"
);
$requete->execute(['idEtudiant' => $currentUser['id'], 'idEtudiantRecent' => $currentUser['id']]);
$seances = $requete->fetchAll();

$pageTitle = 'Emploi du temps';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>

<div class="director-heading"><div><h2>Emploi du temps</h2><p>Les séances associées à votre dernière inscription validée.</p></div></div>
<div class="card table-card" style="overflow-x:auto;">
    <table class="director-table">
        <thead><tr><th>Date</th><th>Horaire</th><th>Module</th><th>Formateur</th><th>Statut</th></tr></thead>
        <tbody>
        <?php foreach ($seances as $seance): ?>
            <tr>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($seance['dateSeance']))) ?></td>
                <td><?= htmlspecialchars(substr($seance['heureDebut'], 0, 5) . ' - ' . substr($seance['heureFin'], 0, 5)) ?></td>
                <td><strong><?= htmlspecialchars($seance['module']) ?></strong></td>
                <td><?= htmlspecialchars(trim(($seance['formateurPrenom'] ?? '') . ' ' . ($seance['formateurNom'] ?? '')) ?: 'Non renseigné') ?></td>
                <td><?= htmlspecialchars($seance['statut']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$seances): ?><tr><td colspan="5">Aucune séance disponible pour le moment.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>