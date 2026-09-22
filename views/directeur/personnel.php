<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../models/Directeur.php';
requireRole(['directeur']);
$db = database();
$currentUser = currentUser();
$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
	$id = (int) ($_POST['idUtilisateur'] ?? 0);
	$tables = ['directeur', 'responsable_pedagogique', 'formateur', 'comptable'];
	if ($id <= 0 || $id === (int) $currentUser['id']) {
		flashMessage('error', 'Votre propre compte ne peut pas être supprimé.');
	} else {
		try {
			$db->beginTransaction();
			foreach ($tables as $table) {
				$requete = $db->prepare('DELETE FROM ' . $table . ' WHERE idUtilisateur = :id');
				$requete->execute(['id' => $id]);
			}
			$requete = $db->prepare('DELETE FROM utilisateur WHERE idUtilisateur = :id');
			$requete->execute(['id' => $id]);
			$db->commit();
			journaliserAction('Suppression d’un membre du personnel', 'Utilisateur #' . $id);
			flashMessage('success', 'Le membre du personnel a été supprimé.');
		} catch (Throwable $exception) {
			if ($db->inTransaction()) $db->rollBack();
			flashMessage('error', 'Ce membre ne peut pas être supprimé car il est encore utilisé dans le système.');
		}
	}
	header('Location: ' . BASE_URL . 'views/directeur/personnel.php');
	exit;
}

$personnel = Directeur::personnel();
$pageTitle = 'Personnel';
$activeMenu = 'personnel';
$contentClass = 'director-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Personnel</h2><p>Gestion des membres du personnel (<?= count($personnel) ?>).</p></div><a class="btn" href="<?= htmlspecialchars(BASE_URL . 'views/directeur/personnel_form.php') ?>">Ajouter un membre</a></div>
<div class="card director-panel" style="overflow-x:auto;"><table class="director-table"><thead><tr><th>Nom complet</th><th>Rôle</th><th>E-mail</th><th>Téléphone</th><th>Statut</th><th>Actions</th></tr></thead><tbody><?php foreach ($personnel as $membre): ?><tr><td><strong><?= htmlspecialchars(trim($membre['prenom'] . ' ' . $membre['nom'])) ?></strong></td><td><?= htmlspecialchars($membre['role']) ?></td><td><?= htmlspecialchars($membre['email']) ?></td><td><?= htmlspecialchars($membre['telephone'] ?: '-') ?></td><td><?= htmlspecialchars($membre['statutCompte']) ?></td><td><a class="btn" style="padding:6px 10px;font-size:12px;" href="<?= htmlspecialchars(BASE_URL . 'views/directeur/personnel_form.php?id=' . $membre['idUtilisateur']) ?>">Modifier</a><?php if ((int) $membre['idUtilisateur'] !== (int) $currentUser['id']): ?><form method="post" style="display:inline" onsubmit="return confirm('Supprimer définitivement ce membre du personnel ?');"><input type="hidden" name="action" value="supprimer"><input type="hidden" name="idUtilisateur" value="<?= (int) $membre['idUtilisateur'] ?>"><button class="btn" style="padding:6px 10px;font-size:12px;background:#b42318;" type="submit">Supprimer</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>