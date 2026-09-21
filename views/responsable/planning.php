<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idSeance'])) {
        try {
            $statement = database()->prepare("DELETE FROM seance WHERE idSeance = :id");
            $statement->execute(['id' => (int) $_POST['idSeance']]);
            $message = "Séance supprimée avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer cette séance : des présences y sont déjà enregistrées.";
        }
    } elseif ($action === 'enregistrer') {
        $idSeance   = isset($_POST['idSeance']) && $_POST['idSeance'] !== '' ? (int) $_POST['idSeance'] : null;
        $idModule   = (int) ($_POST['idModule'] ?? 0);
        $dateSeance = trim($_POST['dateSeance'] ?? '');
        $heureDebut = trim($_POST['heureDebut'] ?? '');
        $heureFin   = trim($_POST['heureFin'] ?? '');
        $statut     = trim($_POST['statut'] ?? 'PLANIFIEE');

        if ($idModule <= 0 || $dateSeance === '' || $heureDebut === '' || $heureFin === '') {
            $erreur = "Tous les champs sont obligatoires.";
            $idEdition = $idSeance;
        } else {
            $moduleStatement = database()->prepare("SELECT idFormateur FROM module WHERE idModule = :id");
            $moduleStatement->execute(['id' => $idModule]);
            $idFormateur = $moduleStatement->fetchColumn();

            if (!$idFormateur) {
                $erreur = "Ce module n'a pas encore de formateur affecté. Affectez d'abord un formateur.";
                $idEdition = $idSeance;
            } elseif ($idSeance) {
                $statement = database()->prepare(
                    "UPDATE seance SET idModule = :idModule, idFormateur = :idFormateur, dateSeance = :dateSeance,
                            heureDebut = :heureDebut, heureFin = :heureFin, statut = :statut
                     WHERE idSeance = :id"
                );
                $statement->execute([
                    'idModule' => $idModule, 'idFormateur' => $idFormateur, 'dateSeance' => $dateSeance,
                    'heureDebut' => $heureDebut, 'heureFin' => $heureFin, 'statut' => $statut, 'id' => $idSeance,
                ]);
                $message = "Séance modifiée avec succès.";
            } else {
                $statement = database()->prepare(
                    "INSERT INTO seance (idModule, idFormateur, dateSeance, heureDebut, heureFin, statut)
                     VALUES (:idModule, :idFormateur, :dateSeance, :heureDebut, :heureFin, 'PLANIFIEE')"
                );
                $statement->execute([
                    'idModule' => $idModule, 'idFormateur' => $idFormateur, 'dateSeance' => $dateSeance,
                    'heureDebut' => $heureDebut, 'heureFin' => $heureFin,
                ]);
                $message = "Séance planifiée avec succès.";
            }
        }
    }
}

$formulaire = ['idSeance' => '', 'idModule' => '', 'dateSeance' => '', 'heureDebut' => '', 'heureFin' => '', 'statut' => 'PLANIFIEE'];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM seance WHERE idSeance = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$seances = database()->query(
    "SELECT sn.idSeance, sn.dateSeance, sn.heureDebut, sn.heureFin, sn.statut,
            m.libelle AS moduleLibelle, u.nom AS formateurNom, u.prenom AS formateurPrenom
     FROM seance sn
     INNER JOIN module m ON m.idModule = sn.idModule
     INNER JOIN utilisateur u ON u.idUtilisateur = sn.idFormateur
     ORDER BY sn.dateSeance DESC, sn.heureDebut DESC
     LIMIT 100"
)->fetchAll();

$modules = database()->query(
    "SELECT m.idModule, m.libelle, m.idFormateur, n.libelle AS niveauLibelle
     FROM module m
     INNER JOIN semestre s ON s.idSemestre = m.idSemestre
     INNER JOIN niveau n ON n.idNiveau = s.idNiveau
     ORDER BY n.libelle, m.libelle"
)->fetchAll();

$pageTitle  = 'Planning';
$activeMenu = 'planning';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Planning des séances</h2>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="card" style="overflow-x:auto; margin-bottom:24px;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Date</th>
                <th style="padding:10px; text-align:left;">Horaire</th>
                <th style="padding:10px; text-align:left;">Module</th>
                <th style="padding:10px; text-align:left;">Formateur</th>
                <th style="padding:10px; text-align:left;">Statut</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($seances as $seance): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($seance['dateSeance']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars(substr($seance['heureDebut'], 0, 5) . ' - ' . substr($seance['heureFin'], 0, 5)) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($seance['moduleLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($seance['formateurPrenom'] . ' ' . $seance['formateurNom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($seance['statut']) ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $seance['idSeance'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Détails / Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette séance ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idSeance" value="<?= $seance['idSeance'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$seances): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune séance planifiée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier la séance' : 'Planifier une séance' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idSeance" value="<?= htmlspecialchars((string) $formulaire['idSeance']) ?>">
        <div class="form-field">
            <label for="idModule">Module</label>
            <select id="idModule" name="idModule" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= $module['idModule'] ?>" <?= (int) $formulaire['idModule'] === (int) $module['idModule'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($module['niveauLibelle'] . ' - ' . $module['libelle']) ?>
                        <?= $module['idFormateur'] ? '' : ' (sans formateur)' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="dateSeance">Date</label>
            <input type="date" id="dateSeance" name="dateSeance" value="<?= htmlspecialchars($formulaire['dateSeance']) ?>" required>
        </div>
        <div class="form-field">
            <label for="heureDebut">Heure de début</label>
            <input type="time" id="heureDebut" name="heureDebut" value="<?= htmlspecialchars(substr($formulaire['heureDebut'], 0, 5)) ?>" required>
        </div>
        <div class="form-field">
            <label for="heureFin">Heure de fin</label>
            <input type="time" id="heureFin" name="heureFin" value="<?= htmlspecialchars(substr($formulaire['heureFin'], 0, 5)) ?>" required>
        </div>
        <?php if ($idEdition): ?>
            <div class="form-field">
                <label for="statut">Statut</label>
                <select id="statut" name="statut" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <?php foreach (['PLANIFIEE', 'EN_COURS', 'TERMINEE', 'ANNULEE'] as $s): ?>
                        <option value="<?= $s ?>" <?= $formulaire['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Planifier la séance' ?></button>
        <?php if ($idEdition): ?>
            <a href="planning.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
