<?php
require_once __DIR__ . '/../../controllers/helpers.php'; require_once __DIR__ . '/../../controllers/InscriptionController.php'; require_once __DIR__ . '/../../controllers/EspaceEtudiantController.php';
$user = requireRole(['etudiant']); $message = ''; $erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = (new InscriptionController())->demanderInscription((int) $user['id'], (int) ($_POST['idNiveau'] ?? 0), $_POST['anneeScolaire'] ?? '');
    if ($ok) $message = 'Votre demande a été enregistrée.'; else $erreur = 'Demande invalide, déjà existante ou impossible à enregistrer.';
}
$niveaux = (new EspaceEtudiantController())->niveaux(); $pageTitle = 'Demande d’inscription'; $showSidebar = true; require __DIR__ . '/../../includes/header.php';
?>
<h1>Demande d’inscription</h1><?php if ($message): ?><div class="card"><?= htmlspecialchars($message) ?></div><?php endif; ?><?php if ($erreur): ?><div class="card"><?= htmlspecialchars($erreur) ?></div><?php endif; ?><div class="card" style="max-width:520px"><form method="post"><label>Année scolaire<input name="anneeScolaire" pattern="[0-9]{4}-[0-9]{4}" placeholder="2026-2027" required></label><label>Niveau<select name="idNiveau" required><option value="">Sélectionner</option><?php foreach ($niveaux as $niveau): ?><option value="<?= (int) $niveau['idNiveau'] ?>"><?= htmlspecialchars($niveau['libelle']) ?></option><?php endforeach; ?></select></label><button class="btn" type="submit">Soumettre la demande</button></form></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>