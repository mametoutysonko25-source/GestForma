<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$currentUser = requireRole(['etudiant']);
$db = database();
$message = null;
$erreur = null;
$evaluationSelectionnee = (int) ($_GET['evaluation'] ?? $_POST['idEvaluation'] ?? 0);
$uploadDirectory = __DIR__ . '/../../assets/uploads/devoirs';

$evaluationsQuery = $db->prepare(
    "SELECT DISTINCT e.idEvaluation, e.titre, e.description, e.dateLimite, m.libelle AS module
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN semestre se ON se.idNiveau = i.idNiveau
     INNER JOIN module m ON m.idSemestre = se.idSemestre
     INNER JOIN evaluation e ON e.idModule = m.idModule
     WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
       AND i.idInscription = (
           SELECT MAX(i2.idInscription)
           FROM inscription i2
           INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
           WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
       )
     ORDER BY e.dateLimite ASC"
);
$evaluationsQuery->execute(['idEtudiant' => $currentUser['id'], 'idEtudiantRecent' => $currentUser['id']]);
$evaluations = $evaluationsQuery->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fichier = $_FILES['fichier'] ?? null;
    $verification = $db->prepare(
        "SELECT e.idEvaluation, e.dateLimite
         FROM inscription i
         INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
         INNER JOIN semestre se ON se.idNiveau = i.idNiveau
         INNER JOIN module m ON m.idSemestre = se.idSemestre
         INNER JOIN evaluation e ON e.idModule = m.idModule
         WHERE d.idEtudiant = :idEtudiant AND i.statut = 'VALIDEE'
           AND e.idEvaluation = :evaluation
           AND i.idInscription = (
               SELECT MAX(i2.idInscription)
               FROM inscription i2
               INNER JOIN dossier_etudiant d2 ON d2.idDossier = i2.idDossier
               WHERE d2.idEtudiant = :idEtudiantRecent AND i2.statut = 'VALIDEE'
           )"
    );
    $verification->execute([
        'idEtudiant' => $currentUser['id'],
        'evaluation' => $evaluationSelectionnee,
        'idEtudiantRecent' => $currentUser['id'],
    ]);
    $evaluationAutorisee = $verification->fetch();
    $extension = $fichier && isset($fichier['name']) ? strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION)) : '';
    $extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'];

    if (!$evaluationAutorisee || !$fichier || $fichier['error'] !== UPLOAD_ERR_OK || !in_array($extension, $extensions, true) || $fichier['size'] > 10 * 1024 * 1024) {
        $erreur = 'Sélectionnez une évaluation valide et un fichier accepté de 10 Mo maximum.';
    } elseif (strtotime($evaluationAutorisee['dateLimite']) < time()) {
        $erreur = 'La date limite de cette évaluation est dépassée.';
    } else {
        $dejaDepose = $db->prepare('SELECT idDepot FROM depot WHERE idEvaluation = :evaluation AND idEtudiant = :etudiant');
        $dejaDepose->execute(['evaluation' => $evaluationSelectionnee, 'etudiant' => $currentUser['id']]);
        if ($dejaDepose->fetchColumn()) {
            $erreur = 'Vous avez déjà rendu cette évaluation.';
        } else {
            if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
            $nomStockage = bin2hex(random_bytes(16)) . '.' . $extension;
            if (move_uploaded_file($fichier['tmp_name'], $uploadDirectory . '/' . $nomStockage)) {
                $idDepot = (int) $db->query('SELECT COALESCE(MAX(idDepot), 0) + 1 FROM depot')->fetchColumn();
                $depot = $db->prepare('INSERT INTO depot (idDepot, idEvaluation, idEtudiant, fichier) VALUES (:idDepot, :evaluation, :etudiant, :fichier)');
                $depot->execute([
                    'idDepot' => $idDepot,
                    'evaluation' => $evaluationSelectionnee,
                    'etudiant' => $currentUser['id'],
                    'fichier' => 'assets/uploads/devoirs/' . $nomStockage,
                ]);
                journaliserAction('Remise d’une évaluation', 'Évaluation #' . $evaluationSelectionnee);
                $message = 'Votre évaluation a été rendue avec succès.';
            } else {
                $erreur = 'Le fichier n’a pas pu être enregistré.';
            }
        }
    }
}

$pageTitle = 'Rendre une évaluation';
$showSidebar = true;
$contentClass = 'management-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Rendre une évaluation</h2><p>Déposez votre travail avant la date limite indiquée.</p></div></div>
<?php if ($message): ?><div class="form-success" role="status"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="form-error" role="alert"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
<div class="card form-card" style="max-width:620px;">
    <form method="post" enctype="multipart/form-data">
        <label>Évaluation
            <select name="idEvaluation" required>
                <option value="">Sélectionner une évaluation</option>
                <?php foreach ($evaluations as $evaluation): ?>
                    <option value="<?= (int) $evaluation['idEvaluation'] ?>" <?= (int) $evaluation['idEvaluation'] === $evaluationSelectionnee ? 'selected' : '' ?>><?= htmlspecialchars($evaluation['module'] . ' - ' . $evaluation['titre'] . ' (limite : ' . date('d/m/Y H:i', strtotime($evaluation['dateLimite'])) . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Fichier du travail
            <input type="file" name="fichier" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
        </label>
        <p class="muted-label">Formats acceptés : PDF, Word, PowerPoint, Excel, image ou ZIP. Taille maximale : 10 Mo.</p>
        <button class="btn btn-primary" type="submit">Envoyer mon travail</button>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
