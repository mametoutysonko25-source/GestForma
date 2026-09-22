<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idModule'])) {
        try {
            $statement = database()->prepare("DELETE FROM module WHERE idModule = :id");
            $statement->execute(['id' => (int) $_POST['idModule']]);
            $message = "Module supprimé avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer ce module : des séances ou évaluations y sont encore rattachées.";
        }
    } elseif ($action === 'enregistrer') {
        $idModule      = isset($_POST['idModule']) && $_POST['idModule'] !== '' ? (int) $_POST['idModule'] : null;
        $idSemestre    = (int) ($_POST['idSemestre'] ?? 0);
        $libelle       = trim($_POST['libelle'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $volumeHoraire = $_POST['volumeHoraire'] !== '' ? (int) $_POST['volumeHoraire'] : null;

        if ($idSemestre <= 0 || $libelle === '') {
            $erreur = "Le semestre et le libellé du module sont obligatoires.";
            $idEdition = $idModule;
        } elseif ($idModule) {
            $statement = database()->prepare(
                "UPDATE module SET idSemestre = :idSemestre, libelle = :libelle, description = :description, volumeHoraire = :volumeHoraire
                 WHERE idModule = :id"
            );
            $statement->execute([
                'idSemestre' => $idSemestre, 'libelle' => $libelle,
                'description' => $description ?: null, 'volumeHoraire' => $volumeHoraire, 'id' => $idModule,
            ]);
            $message = "Module « " . $libelle . " » modifié avec succès.";
        } else {
            $statement = database()->prepare(
                "INSERT INTO module (idSemestre, libelle, description, volumeHoraire)
                 VALUES (:idSemestre, :libelle, :description, :volumeHoraire)"
            );
            $statement->execute([
                'idSemestre' => $idSemestre, 'libelle' => $libelle,
                'description' => $description ?: null, 'volumeHoraire' => $volumeHoraire,
            ]);
            $message = "Module « " . $libelle . " » ajouté avec succès.";
        }
    }
}

$formulaire = ['idModule' => '', 'idSemestre' => '', 'libelle' => '', 'description' => '', 'volumeHoraire' => ''];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM module WHERE idModule = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$modules = database()->query(
    "SELECT m.idModule, m.libelle, m.volumeHoraire,
            s.libelle AS semestreLibelle, n.libelle AS niveauLibelle,
            u.nom AS formateurNom, u.prenom AS formateurPrenom
     FROM module m
     INNER JOIN semestre s ON s.idSemestre = m.idSemestre
     INNER JOIN niveau n ON n.idNiveau = s.idNiveau
     LEFT JOIN formateur f ON f.idUtilisateur = m.idFormateur
     LEFT JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
     ORDER BY n.libelle, s.libelle, m.libelle"
)->fetchAll();

$semestres = database()->query(
    "SELECT s.idSemestre, s.libelle, n.libelle AS niveauLibelle
     FROM semestre s
     INNER JOIN niveau n ON n.idNiveau = s.idNiveau
     ORDER BY n.libelle, s.libelle"
)->fetchAll();

$pageTitle  = 'Modules';
$activeMenu = 'modules';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Modules (<?= count($modules) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Semestre</th>
                <th style="padding:10px; text-align:left;">Module</th>
                <th style="padding:10px; text-align:left;">Volume horaire</th>
                <th style="padding:10px; text-align:left;">Formateur</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $module): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($module['niveauLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($module['semestreLibelle']) ?></td>
                    <td style="padding:10px;"><strong><?= htmlspecialchars($module['libelle']) ?></strong></td>
                    <td style="padding:10px;"><?= $module['volumeHoraire'] !== null ? htmlspecialchars($module['volumeHoraire']) . ' h' : '-' ?></td>
                    <td style="padding:10px;">
                        <?php if ($module['formateurNom']): ?>
                            <?= htmlspecialchars($module['formateurPrenom'] . ' ' . $module['formateurNom']) ?>
                        <?php else: ?>
                            <span style="color:#c62828;">Non affecté</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $module['idModule'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce module ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idModule" value="<?= $module['idModule'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$modules): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucun module enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier le module' : 'Ajouter un module' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idModule" value="<?= htmlspecialchars((string) $formulaire['idModule']) ?>">
        <div class="form-field">
            <label for="idSemestre">Semestre</label>
            <select id="idSemestre" name="idSemestre" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($semestres as $semestre): ?>
                    <option value="<?= $semestre['idSemestre'] ?>" <?= (int) $formulaire['idSemestre'] === (int) $semestre['idSemestre'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($semestre['niveauLibelle'] . ' - ' . $semestre['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="libelle">Libellé du module</label>
            <input type="text" id="libelle" name="libelle" value="<?= htmlspecialchars($formulaire['libelle']) ?>" required>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?= htmlspecialchars($formulaire['description'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label for="volumeHoraire">Volume horaire (heures)</label>
            <input type="number" id="volumeHoraire" name="volumeHoraire" min="1" value="<?= htmlspecialchars((string) ($formulaire['volumeHoraire'] ?? '')) ?>">
        </div>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter le module' ?></button>
        <?php if ($idEdition): ?>
            <a href="modules.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<p style="margin-top:16px; font-size:13px; color:var(--muted);">
    L'affectation d'un formateur à un module se fait depuis la page <a href="affectation.php" style="color:var(--primary);">Affectations</a>.
</p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
