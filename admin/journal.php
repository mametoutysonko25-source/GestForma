<?php
require_once __DIR__ . '/../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);
$db = database();

$db->exec(
	'CREATE TABLE IF NOT EXISTS historique (
		idHistorique int NOT NULL AUTO_INCREMENT,
		utilisateurId int NOT NULL,
		action varchar(255) NOT NULL,
		dateAction datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		details text DEFAULT NULL,
		PRIMARY KEY (idHistorique),
		KEY utilisateurId (utilisateurId)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);
$db->exec('ALTER TABLE historique MODIFY idHistorique INT NOT NULL AUTO_INCREMENT');
try { $db->exec('ALTER TABLE historique ADD PRIMARY KEY (idHistorique)'); } catch (Throwable $exception) {}

$db->exec(
	'CREATE TABLE IF NOT EXISTS historique_archive (
		idHistorique int NOT NULL AUTO_INCREMENT,
		utilisateurId int NOT NULL,
		action varchar(255) NOT NULL,
		dateAction datetime NOT NULL,
		details text DEFAULT NULL,
		dateArchivage datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		archivePar int NOT NULL,
		PRIMARY KEY (idHistorique),
		KEY utilisateurId (utilisateurId),
		KEY archivePar (archivePar)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);
$db->exec('ALTER TABLE historique_archive MODIFY idHistorique INT NOT NULL AUTO_INCREMENT');
try { $db->exec('ALTER TABLE historique_archive ADD PRIMARY KEY (idHistorique)'); } catch (Throwable $exception) {}

$message = null;
$erreur = null;
$debutSemaine = date('Y-m-d', strtotime('monday this week'));
try {
	$db->beginTransaction();
	$copieHebdomadaire = $db->prepare(
		'INSERT INTO historique_archive (idHistorique, utilisateurId, action, dateAction, details, archivePar)
		 SELECT idHistorique, utilisateurId, action, dateAction, details, :archivePar
		 FROM historique WHERE dateAction < :debutSemaine'
	);
	$copieHebdomadaire->execute(['archivePar' => $currentUser['id'], 'debutSemaine' => $debutSemaine . ' 00:00:00']);
	$suppressionHebdomadaire = $db->prepare('DELETE FROM historique WHERE dateAction < :debutSemaine');
	$suppressionHebdomadaire->execute(['debutSemaine' => $debutSemaine . ' 00:00:00']);
	$db->commit();
	if ($suppressionHebdomadaire->rowCount() > 0) {
		$message = $suppressionHebdomadaire->rowCount() . ' journal(aux) des semaines précédentes archivé(s) automatiquement.';
	}
} catch (Throwable $exception) {
	if ($db->inTransaction()) $db->rollBack();
	$erreur = 'L’archivage automatique des semaines précédentes a échoué.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';
	try {
		$db->beginTransaction();
		if ($action === 'archiver' && (int) ($_POST['idHistorique'] ?? 0) > 0) {
			$idHistorique = (int) $_POST['idHistorique'];
			$copie = $db->prepare(
				'INSERT INTO historique_archive (idHistorique, utilisateurId, action, dateAction, details, archivePar)
				 SELECT idHistorique, utilisateurId, action, dateAction, details, :archivePar FROM historique WHERE idHistorique = :id'
			);
			$copie->execute(['archivePar' => $currentUser['id'], 'id' => $idHistorique]);
			$suppression = $db->prepare('DELETE FROM historique WHERE idHistorique = :id');
			$suppression->execute(['id' => $idHistorique]);
			$message = 'Le journal a été archivé.';
		} elseif ($action === 'archiver_avant' && !empty($_POST['dateAvant'])) {
			$copie = $db->prepare(
				'INSERT INTO historique_archive (idHistorique, utilisateurId, action, dateAction, details, archivePar)
				 SELECT idHistorique, utilisateurId, action, dateAction, details, :archivePar FROM historique WHERE dateAction < :dateAvant'
			);
			$copie->execute(['archivePar' => $currentUser['id'], 'dateAvant' => $_POST['dateAvant'] . ' 00:00:00']);
			$suppression = $db->prepare('DELETE FROM historique WHERE dateAction < :dateAvant');
			$suppression->execute(['dateAvant' => $_POST['dateAvant'] . ' 00:00:00']);
			$message = $suppression->rowCount() . ' journal(aux) archivé(s).';
		}
		$db->commit();
	} catch (Throwable $exception) {
		if ($db->inTransaction()) $db->rollBack();
		$erreur = 'Impossible d’archiver ce journal.';
	}
}

$actions = $db->query(
	'SELECT h.*, u.prenom, u.nom, u.email FROM historique h
	 INNER JOIN utilisateur u ON u.idUtilisateur = h.utilisateurId
	 ORDER BY h.dateAction DESC, h.idHistorique DESC LIMIT 200'
)->fetchAll();
$archives = $db->query(
	'SELECT h.*, u.prenom, u.nom, u.email FROM historique_archive h
	 INNER JOIN utilisateur u ON u.idUtilisateur = h.utilisateurId
	 ORDER BY h.dateArchivage DESC, h.idHistorique DESC LIMIT 200'
)->fetchAll();
$pageTitle = 'Journal des actions';
$activeMenu = 'journal';
$contentClass = 'management-content';
require __DIR__ . '/../includes/header.php';
?>
<div class="director-heading"><div><h2>Journal des actions</h2><p><?= count($actions) ?> journal(aux) actif(s), <?= count($archives) ?> archive(s). Les semaines précédentes sont archivées automatiquement à l’ouverture de cette page.</p></div></div>
<?php if ($message): ?><div class="form-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="form-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
<div class="card form-card"><form method="post" class="inline-form"><input type="hidden" name="action" value="archiver_avant"><label for="dateAvant">Archiver les journaux antérieurs au</label><input type="date" id="dateAvant" name="dateAvant" required><button class="btn btn-primary" type="submit">Archiver</button></form></div>
<div class="card table-card"><table class="director-table"><thead><tr><th>Date et heure</th><th>Utilisateur</th><th>E-mail</th><th>Action</th><th>Détails</th><th>Gestion</th></tr></thead><tbody><?php foreach ($actions as $action): ?><tr><td><?= htmlspecialchars($action['dateAction']) ?></td><td><?= htmlspecialchars($action['prenom'].' '.$action['nom']) ?></td><td><?= htmlspecialchars($action['email']) ?></td><td><?= htmlspecialchars($action['action']) ?></td><td><?= htmlspecialchars($action['details'] ?: '-') ?></td><td><form method="post" onsubmit="return confirm('Archiver ce journal ?');"><input type="hidden" name="action" value="archiver"><input type="hidden" name="idHistorique" value="<?= (int) $action['idHistorique'] ?>"><button class="btn btn-small" type="submit">Archiver</button></form></td></tr><?php endforeach; ?><?php if (!$actions): ?><tr><td colspan="6">Aucun journal actif.</td></tr><?php endif; ?></tbody></table></div>
<div class="card table-card"><h3>Archives</h3><table class="director-table"><thead><tr><th>Date d’action</th><th>Utilisateur</th><th>Action</th><th>Archivé le</th></tr></thead><tbody><?php foreach ($archives as $archive): ?><tr><td><?= htmlspecialchars($archive['dateAction']) ?></td><td><?= htmlspecialchars($archive['prenom'].' '.$archive['nom']) ?></td><td><?= htmlspecialchars($archive['action']) ?></td><td><?= htmlspecialchars($archive['dateArchivage']) ?></td></tr><?php endforeach; ?><?php if (!$archives): ?><tr><td colspan="4">Aucune archive.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>