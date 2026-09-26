<?php require __DIR__ . '/_bootstrap.php';
$message = null; $erreur = null;
$uploadDirectory = __DIR__ . '/../../assets/uploads/supports';
$db->exec('ALTER TABLE support ADD COLUMN IF NOT EXISTS idEvaluation INT NULL, ADD KEY IF NOT EXISTS idEvaluation (idEvaluation)');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $module = (int) ($_POST['idModule'] ?? 0); $titre = trim($_POST['titre'] ?? ''); $date = $_POST['dateLimite'] ?? '';
    $q = $db->prepare('SELECT idModule FROM module WHERE idModule=:module AND idFormateur=:formateur'); $q->execute(['module'=>$module,'formateur'=>$formateurId]);
    if (!$q->fetchColumn() || $titre === '' || $date === '') $erreur = 'Le module, le titre et la date limite sont obligatoires.';
    else { $q=$db->prepare('INSERT INTO evaluation (idModule,titre,dateLimite) VALUES (:module,:titre,:date)'); $q->execute(['module'=>$module,'titre'=>$titre,'date'=>$date]); journaliserAction('Création d’une évaluation',$titre); flashMessage('success','Évaluation ajoutée avec succès.'); header('Location: '.BASE_URL.'views/formateur/evaluations.php'); exit; }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_document') {
    $evaluationId = (int) ($_POST['idEvaluation'] ?? 0);
    $titreDocument = trim($_POST['titreDocument'] ?? '');
    $fichier = $_FILES['fichier'] ?? null;
    $extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    $extension = $fichier && isset($fichier['name']) ? strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION)) : '';
    $verification = $db->prepare(
        'SELECT e.idEvaluation, e.idModule FROM evaluation e INNER JOIN module m ON m.idModule=e.idModule
         WHERE e.idEvaluation=:evaluation AND m.idFormateur=:formateur'
    );
    $verification->execute(['evaluation' => $evaluationId, 'formateur' => $formateurId]);
    $evaluationAutorisee = $verification->fetch();
    if (!$evaluationAutorisee || !$fichier || $fichier['error'] !== UPLOAD_ERR_OK || !in_array($extension, $extensions, true) || $fichier['size'] > 10 * 1024 * 1024) {
        $erreur = 'Sélectionnez un fichier valide de 10 Mo maximum pour cette évaluation.';
    } else {
        if ($titreDocument === '') $titreDocument = pathinfo($fichier['name'], PATHINFO_FILENAME);
        if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
        $nomStockage = bin2hex(random_bytes(16)) . '.' . $extension;
        if (move_uploaded_file($fichier['tmp_name'], $uploadDirectory . '/' . $nomStockage)) {
            $requete = $db->prepare('INSERT INTO support (idModule,idEvaluation,titre,type,fichier) VALUES (:module,:evaluation,:titre,:type,:fichier)');
            $requete->execute([
                'module' => $evaluationAutorisee['idModule'], 'evaluation' => $evaluationId,
                'titre' => $titreDocument, 'type' => 'Document d’évaluation',
                'fichier' => 'assets/uploads/supports/' . $nomStockage,
            ]);
            journaliserAction('Dépôt d’un document d’évaluation', 'Évaluation #' . $evaluationId);
            $message = 'Le fichier de l’évaluation a été ajouté.';
        } else {
            $erreur = 'Le fichier n’a pas pu être enregistré.';
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') { try { $q=$db->prepare('DELETE e FROM evaluation e INNER JOIN module m ON m.idModule=e.idModule WHERE e.idEvaluation=:evaluation AND m.idFormateur=:formateur'); $q->execute(['evaluation'=>(int)$_POST['idEvaluation'],'formateur'=>$formateurId]); journaliserAction('Suppression d’une évaluation','Évaluation #'.(int)$_POST['idEvaluation']); flashMessage('success','Évaluation supprimée.'); } catch (PDOException $exception) { flashMessage('error','Cette évaluation ne peut pas être supprimée car des résultats ou dépôts y sont rattachés.'); } header('Location: '.BASE_URL.'views/formateur/evaluations.php'); exit; }
$q=$db->prepare('SELECT e.*,m.libelle AS module FROM evaluation e INNER JOIN module m ON m.idModule=e.idModule WHERE m.idFormateur=:id ORDER BY e.dateLimite DESC'); $q->execute(['id'=>$formateurId]); $evaluations=$q->fetchAll(); $q=$db->prepare('SELECT idModule,libelle FROM module WHERE idFormateur=:id ORDER BY libelle'); $q->execute(['id'=>$formateurId]); $modules=$q->fetchAll(); $pageTitle='Évaluations'; $activeMenu='evaluations'; require __DIR__.'/../../includes/header.php'; ?>
<div class="card"><h2>Ajouter une évaluation</h2><?php if($erreur): ?><p style="color:#c62828"><?=htmlspecialchars($erreur)?></p><?php endif; ?><form method="post"><input type="hidden" name="action" value="ajouter"><select name="idModule" required><option value="">Module</option><?php foreach($modules as $module): ?><option value="<?=$module['idModule']?>"><?=htmlspecialchars($module['libelle'])?></option><?php endforeach; ?></select> <input name="titre" placeholder="Titre" required> <input type="datetime-local" name="dateLimite" required> <button class="btn" type="submit">Ajouter</button></form></div>
<?php if($message): ?><div class="form-success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<div class="card"><h2>Mes évaluations</h2><table class="director-table"><thead><tr><th>Module</th><th>Titre</th><th>Date limite</th><th>Ajouter le fichier</th><th>Actions</th></tr></thead><tbody><?php foreach($evaluations as $evaluation): ?><tr><td><?=htmlspecialchars($evaluation['module'])?></td><td><?=htmlspecialchars($evaluation['titre'])?></td><td><?=htmlspecialchars($evaluation['dateLimite'])?></td><td><form method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="ajouter_document"><input type="hidden" name="idEvaluation" value="<?=$evaluation['idEvaluation']?>"><input type="file" name="fichier" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"><input type="text" name="titreDocument" maxlength="200" placeholder="Titre facultatif"><button class="btn btn-primary" type="submit">Ajouter</button></form></td><td><a class="btn" href="<?=BASE_URL?>views/formateur/supports.php?evaluation=<?=(int)$evaluation['idEvaluation']?>">Voir les documents</a> <form method="post" style="display:inline"><input type="hidden" name="action" value="supprimer"><input type="hidden" name="idEvaluation" value="<?=$evaluation['idEvaluation']?>"><button class="btn" style="background:#c62828" onclick="return confirm('Supprimer cette évaluation ?')">Supprimer</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php require __DIR__.'/../../includes/footer.php'; ?>