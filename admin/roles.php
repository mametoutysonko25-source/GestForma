<?php
require_once __DIR__ . '/../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);
$db = database();
$roles = ['Administrateur'=>'administrateur','Directeur'=>'directeur','Responsable pédagogique'=>'responsable_pedagogique','Formateur'=>'formateur','Comptable'=>'comptable','Étudiant'=>'etudiant'];
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id = (int) ($_POST['idUtilisateur'] ?? 0);
	if (($_POST['action'] ?? '') === 'statut') {
		$statut = $_POST['statut'] ?? '';
		if ($id <= 0 || !in_array($statut, ['ACTIF', 'INACTIF', 'BLOQUE'], true) || $id === (int) $currentUser['id']) {
			$erreur = 'Votre compte ne peut pas être suspendu ou les données sont invalides.';
		} else {
			$requete = $db->prepare('UPDATE utilisateur SET statutCompte = :statut WHERE idUtilisateur = :id');
			$requete->execute(['statut' => $statut, 'id' => $id]);
			journaliserAction('Modification du statut utilisateur', 'Utilisateur #' . $id . ' : ' . $statut);
			flashMessage('success', $statut === 'ACTIF' ? 'Le compte a été réactivé.' : 'Le compte a été suspendu.');
			header('Location: ' . BASE_URL . 'admin/roles.php');
			exit;
		}
	}
	$role = $_POST['role'] ?? '';
	if (($_POST['action'] ?? '') !== 'statut' && ($id <= 0 || !isset($roles[$role]) || $id === (int) $currentUser['id'])) {
		$erreur = 'Le compte courant ne peut pas changer de rôle ou les données sont invalides.';
	} elseif (($_POST['action'] ?? '') !== 'statut') {
		try {
			$db->beginTransaction();
			foreach ($roles as $table) {
				$requete = $db->prepare('DELETE FROM ' . $table . ' WHERE idUtilisateur = :id');
				$requete->execute(['id' => $id]);
			}
			$table = $roles[$role];
			if (in_array($role, ['Formateur', 'Responsable pédagogique', 'Étudiant'], true)) {
				$prefixe = $role === 'Formateur' ? 'FOR' : ($role === 'Responsable pédagogique' ? 'RP' : 'ETU');
				$requete = $db->prepare('INSERT INTO ' . $table . ' (idUtilisateur, matricule) VALUES (:id, :matricule)');
				$requete->execute(['id' => $id, 'matricule' => $prefixe . str_pad((string) $id, 3, '0', STR_PAD_LEFT)]);
			} else {
				$requete = $db->prepare('INSERT INTO ' . $table . ' (idUtilisateur) VALUES (:id)');
				$requete->execute(['id' => $id]);
			}
			$db->commit();
			journaliserAction('Attribution d’un rôle', 'Utilisateur #' . $id . ' : ' . $role);
			flashMessage('success', 'Le rôle du compte a été modifié avec succès.');
			header('Location: ' . BASE_URL . 'admin/roles.php');
			exit;
		} catch (Throwable $exception) {
			if ($db->inTransaction()) $db->rollBack();
			$erreur = 'Impossible d’attribuer ce rôle. Vérifiez que le matricule est disponible.';
		}
	}
}

$comptes=[]; foreach ($roles as $libelle=>$table) $comptes[$libelle]=(int)$db->query('SELECT COUNT(*) FROM '.$table)->fetchColumn();
$utilisateurs = $db->query(
	"SELECT u.idUtilisateur, u.prenom, u.nom, u.statutCompte,
		CASE
			WHEN a.idUtilisateur IS NOT NULL THEN 'Administrateur'
			WHEN d.idUtilisateur IS NOT NULL THEN 'Directeur'
			WHEN r.idUtilisateur IS NOT NULL THEN 'Responsable pédagogique'
			WHEN f.idUtilisateur IS NOT NULL THEN 'Formateur'
			WHEN c.idUtilisateur IS NOT NULL THEN 'Comptable'
			WHEN e.idUtilisateur IS NOT NULL THEN 'Étudiant'
			ELSE 'Sans rôle'
		END AS role
	 FROM utilisateur u
	 LEFT JOIN administrateur a ON a.idUtilisateur=u.idUtilisateur
	 LEFT JOIN directeur d ON d.idUtilisateur=u.idUtilisateur
	 LEFT JOIN responsable_pedagogique r ON r.idUtilisateur=u.idUtilisateur
	 LEFT JOIN formateur f ON f.idUtilisateur=u.idUtilisateur
	 LEFT JOIN comptable c ON c.idUtilisateur=u.idUtilisateur
	 LEFT JOIN etudiant e ON e.idUtilisateur=u.idUtilisateur
	 ORDER BY u.nom,u.prenom"
)->fetchAll();
$pageTitle='Rôles et accès'; $activeMenu='roles'; $contentClass = 'management-content'; require __DIR__ . '/../includes/header.php';
?>
<div class="director-heading"><div><h2>Rôles et accès</h2><p>Attribuez un rôle fonctionnel à chaque compte actif.</p></div></div>
<?php if ($erreur): ?><div class="form-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
<div class="card table-card"><table class="director-table"><thead><tr><th>Utilisateur</th><th>Statut</th><th>Rôle actuel</th><th>Attribuer un rôle</th><th>Accès</th></tr></thead><tbody><?php foreach ($utilisateurs as $utilisateur): ?><tr><td><strong><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong></td><td><span class="status-badge status-<?= strtolower($utilisateur['statutCompte']) ?>"><?= htmlspecialchars($utilisateur['statutCompte']) ?></span></td><td><span class="role-badge"><?= htmlspecialchars($utilisateur['role']) ?></span></td><td><?php if ((int) $utilisateur['idUtilisateur'] === (int) $currentUser['id']): ?><span class="muted-label">Votre compte</span><?php else: ?><form method="post" class="role-form"><input type="hidden" name="action" value="role"><input type="hidden" name="idUtilisateur" value="<?= (int) $utilisateur['idUtilisateur'] ?>"><select name="role" aria-label="Rôle de <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>"><option value="Administrateur" <?= $utilisateur['role']==='Administrateur'?'selected':'' ?>>Administrateur</option><option value="Directeur" <?= $utilisateur['role']==='Directeur'?'selected':'' ?>>Directeur</option><option value="Responsable pédagogique" <?= $utilisateur['role']==='Responsable pédagogique'?'selected':'' ?>>Responsable pédagogique</option><option value="Formateur" <?= $utilisateur['role']==='Formateur'?'selected':'' ?>>Formateur</option><option value="Comptable" <?= $utilisateur['role']==='Comptable'?'selected':'' ?>>Comptable</option><option value="Étudiant" <?= $utilisateur['role']==='Étudiant'?'selected':'' ?>>Étudiant</option></select><button class="btn btn-small btn-primary" type="submit">Enregistrer</button></form><?php endif; ?></td><td><?php if ((int) $utilisateur['idUtilisateur'] !== (int) $currentUser['id']): ?><form method="post" class="inline-form" onsubmit="return confirm('Modifier l’accès à ce compte ?');"><input type="hidden" name="action" value="statut"><input type="hidden" name="idUtilisateur" value="<?= (int) $utilisateur['idUtilisateur'] ?>"><input type="hidden" name="statut" value="<?= $utilisateur['statutCompte'] === 'ACTIF' ? 'INACTIF' : 'ACTIF' ?>"><button class="btn btn-small <?= $utilisateur['statutCompte'] === 'ACTIF' ? 'btn-danger' : 'btn-success' ?>" type="submit"><?= $utilisateur['statutCompte'] === 'ACTIF' ? 'Suspendre' : 'Réactiver' ?></button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div>
<div class="card role-summary"><h3>Répartition des rôles</h3><div class="role-summary-grid"><?php foreach($comptes as $libelle=>$nombre): ?><div><span><?=htmlspecialchars($libelle)?></span><strong><?=$nombre?></strong></div><?php endforeach; ?></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>