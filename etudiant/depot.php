<?php
require_once __DIR__ . '/../controllers/helpers.php'; require_once __DIR__ . '/../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $controller = new EspaceEtudiantController(); $message = ''; $erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'], $_POST['idEvaluation'])) {
    $upload = $_FILES['fichier']; $extension = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
    $extensionsAutorisees = ['pdf', 'doc', 'docx', 'zip']; $dossier = __DIR__ . '/../uploads/travaux';
    if ($upload['error'] !== UPLOAD_ERR_OK || !in_array($extension, $extensionsAutorisees, true) || $upload['size'] > 10 * 1024 * 1024) {
        $erreur = 'Fichier invalide (PDF, DOC, DOCX ou ZIP, 10 Mo maximum).';
    } else {
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);
        $nom = (int) $user['id'] . '_' . (int) $_POST['idEvaluation'] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        if (move_uploaded_file($upload['tmp_name'], $dossier . '/' . $nom) && $controller->deposer((int) $_POST['idEvaluation'], (int) $user['id'], 'uploads/travaux/' . $nom)) $message = 'Travail déposé avec succès.';
        else $erreur = 'Le dépôt n’a pas pu être enregistré.';
    }
}
$evaluations = $controller->evaluations((int) $user['id']); $pageTitle = 'Déposer un travail'; $showSidebar = true; require __DIR__ . '/../includes/header.php';
?>
<h1>Déposer un travail</h1><?php if ($message): ?><div class="card"><?= htmlspecialchars($message) ?></div><?php endif; ?><?php if ($erreur): ?><div class="card"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
<div class="card" style="max-width:560px"><form method="post" enctype="multipart/form-data"><label for="idEvaluation">Évaluation</label><select name="idEvaluation" id="idEvaluation" required><?php foreach ($evaluations as $evaluation): ?><option value="<?= (int) $evaluation['idEvaluation'] ?>"><?= htmlspecialchars($evaluation['module'] . ' - ' . $evaluation['titre']) ?></option><?php endforeach; ?></select><label for="fichier">Fichier</label><input type="file" name="fichier" id="fichier" required accept=".pdf,.doc,.docx,.zip"><button class="btn" type="submit">Déposer</button></form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>