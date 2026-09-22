<?php
require __DIR__ . '/_bootstrap.php';
$messageErreur = null;
$uploadDirectory = __DIR__ . '/../../assets/uploads/supports';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = (int) ($_POST['idModule'] ?? 0); $titre = trim($_POST['titre'] ?? ''); $type = trim($_POST['type'] ?? 'Support');
    $verification = $db->prepare('SELECT idModule FROM module WHERE idModule=:module AND idFormateur=:formateur'); $verification->execute(['module'=>$module,'formateur'=>$formateurId]);
    $fichier = $_FILES['fichier'] ?? null;
    $extensions = ['pdf','doc','docx','ppt','pptx','xls','xlsx','jpg','jpeg','png'];
    $extension = $fichier && isset($fichier['name']) ? strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION)) : '';
    if (!$verification->fetchColumn() || $titre === '' || !$fichier || $fichier['error'] !== UPLOAD_ERR_OK || !in_array($extension, $extensions, true) || $fichier['size'] > 10 * 1024 * 1024) {
        $messageErreur = 'Sélectionnez un module, un titre et un document valide de 10 Mo maximum.';
    } else {
        if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
        $nomStockage = bin2hex(random_bytes(16)) . '.' . $extension;
        if (move_uploaded_file($fichier['tmp_name'], $uploadDirectory . '/' . $nomStockage)) {
            $requete = $db->prepare('INSERT INTO support (idModule,titre,type,fichier) VALUES (:module,:titre,:type,:fichier)');
            $requete->execute(['module'=>$module,'titre'=>$titre,'type'=>$type,'fichier'=>'assets/uploads/supports/' . $nomStockage]);
            journaliserAction('Dépôt d’un document pédagogique', $titre);
            flashMessage('success', 'Le document a été déposé avec succès.');
            header('Location: ' . BASE_URL . 'views/formateur/supports.php'); exit;
        }
        $messageErreur = 'Le document n’a pas pu être enregistré.';
    }
}
$requete=$db->prepare('SELECT idModule,libelle FROM module WHERE idFormateur=:id ORDER BY libelle'); $requete->execute(['id'=>$formateurId]); $modules=$requete->fetchAll();
$requete=$db->prepare('SELECT s.idSupport,s.titre,s.type,s.fichier,s.dateAjout,m.libelle AS module FROM support s INNER JOIN module m ON m.idModule=s.idModule WHERE m.idFormateur=:id ORDER BY s.dateAjout DESC'); $requete->execute(['id'=>$formateurId]); $supports=$requete->fetchAll();
$pageTitle='Documents pédagogiques'; $activeMenu='supports'; require __DIR__.'/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Documents pédagogiques</h2><p>Déposez les supports nécessaires à vos modules et évaluations.</p></div></div>
<?php if ($messageErreur): ?><div class="form-error"><?= htmlspecialchars($messageErreur) ?></div><?php endif; ?>
<div class="card form-card"><div class="section-heading"><h2>Nouveau document</h2><p>Formats acceptés : PDF, Word, PowerPoint, Excel et images. Taille maximale : 10 Mo.</p></div><form method="post" enctype="multipart/form-data"><div class="form-grid"><label>Module<select name="idModule" required><option value="">Sélectionner un module</option><?php foreach($modules as $module): ?><option value="<?= (int)$module['idModule'] ?>"><?= htmlspecialchars($module['libelle']) ?></option><?php endforeach; ?></select></label><label>Type<select name="type"><option>Support de cours</option><option>Document d’évaluation</option><option>Corrigé</option><option>Autre</option></select></label><label class="full-field">Titre du document<input name="titre" required maxlength="200" placeholder="Ex. Chapitre 1 - Introduction"></label><label class="full-field">Fichier<input type="file" name="fichier" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"></label></div><div class="form-actions"><button class="btn btn-primary" type="submit">Déposer le document</button></div></form></div>
<div class="card table-card"><table class="director-table"><thead><tr><th>Document</th><th>Module</th><th>Type</th><th>Déposé le</th><th>Accès</th></tr></thead><tbody><?php foreach($supports as $support): ?><tr><td><strong><?= htmlspecialchars($support['titre']) ?></strong></td><td><?= htmlspecialchars($support['module']) ?></td><td><span class="role-badge"><?= htmlspecialchars($support['type']) ?></span></td><td><?= htmlspecialchars($support['dateAjout']) ?></td><td><a class="btn btn-small btn-secondary" href="<?= htmlspecialchars(BASE_URL . $support['fichier']) ?>" target="_blank" rel="noopener">Ouvrir</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__.'/../../includes/footer.php'; ?>