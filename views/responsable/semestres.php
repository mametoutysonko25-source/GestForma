<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idSemestre'])) {
        try {
            $statement = database()->prepare("DELETE FROM semestre WHERE idSemestre = :id");
            $statement->execute(['id' => (int) $_POST['idSemestre']]);
            $message = "Semestre supprimé avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer ce semestre : des modules y sont encore rattachés.";
        }
    } elseif ($action === 'enregistrer') {
        $idSemestre  = isset($_POST['idSemestre']) && $_POST['idSemestre'] !== '' ? (int) $_POST['idSemestre'] : null;
        $idNiveau    = (int) ($_POST['idNiveau'] ?? 0);
        $libelle     = trim($_POST['libelle'] ?? '');
        $ordre       = (int) ($_POST['ordre'] ?? 1);
        $description = trim($_POST['description'] ?? '');

        if ($idNiveau <= 0 || $libelle === '') {
            $erreur = "Le niveau et le libellé du semestre sont obligatoires.";
            $idEdition = $idSemestre;
        } elseif ($idSemestre) {
            $statement = database()->prepare(
                "UPDATE semestre SET idNiveau = :idNiveau, libelle = :libelle, ordre = :ordre, description = :description WHERE idSemestre = :id"
            );
            $statement->execute([
                'idNiveau' => $idNiveau,
                'libelle' => $libelle,
                'ordre' => $ordre,
                'description' => $description !== '' ? $description : null,
                'id' => $idSemestre,
            ]);
            $message = "Semestre « " . $libelle . " » modifié avec succès.";
        } else {
            $statement = database()->prepare(
                "INSERT INTO semestre (idNiveau, libelle, ordre, description) VALUES (:idNiveau, :libelle, :ordre, :description)"
            );
            $statement->execute([
                'idNiveau' => $idNiveau,
                'libelle' => $libelle,
                'ordre' => $ordre,
                'description' => $description !== '' ? $description : null,
            ]);
            $message = "Semestre « " . $libelle . " » ajouté avec succès.";
        }
    }
}

$formulaire = ['idSemestre' => '', 'idNiveau' => '', 'libelle' => '', 'ordre' => 1, 'description' => ''];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM semestre WHERE idSemestre = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$semestres = database()->query(
    "SELECT s.idSemestre, s.libelle, s.ordre, s.description, n.libelle AS niveauLibelle, f.libelle AS formationLibelle
     FROM semestre s
     INNER JOIN niveau n ON n.idNiveau = s.idNiveau
     INNER JOIN formation f ON f.idFormation = n.idFormation
     ORDER BY f.libelle, n.ordre, s.ordre"
)->fetchAll();

$niveaux = database()->query(
    "SELECT n.idNiveau, n.libelle, f.libelle AS formationLibelle
     FROM niveau n
     INNER JOIN formation f ON f.idFormation = n.idFormation
     ORDER BY f.libelle, n.ordre"
)->fetchAll();

$pageTitle  = 'Semestres';
$activeMenu = 'semestres';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Semestres (<?= count($semestres) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Formation</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Semestre</th>
                <th style="padding:10px; text-align:left;">Ordre</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($semestres as $semestre): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($semestre['formationLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($semestre['niveauLibelle']) ?></td>
                    <td style="padding:10px;"><strong><?= htmlspecialchars($semestre['libelle']) ?></strong></td>
                    <td style="padding:10px;"><?= (int) $semestre['ordre'] ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $semestre['idSemestre'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce semestre ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idSemestre" value="<?= $semestre['idSemestre'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$semestres): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucun semestre enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier le semestre' : 'Ajouter un semestre' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idSemestre" value="<?= htmlspecialchars((string) $formulaire['idSemestre']) ?>">
        <div class="form-field">
            <label for="idNiveau">Niveau</label>
            <select id="idNiveau" name="idNiveau" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($niveaux as $niveau): ?>
                    <option value="<?= $niveau['idNiveau'] ?>" <?= (int) $formulaire['idNiveau'] === (int) $niveau['idNiveau'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($niveau['formationLibelle'] . ' - ' . $niveau['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="libelle">Libellé du semestre</label>
            <input type="text" id="libelle" name="libelle" value="<?= htmlspecialchars($formulaire['libelle']) ?>" required placeholder="Ex : Semestre 1">
        </div>
        <div class="form-field">
            <label for="ordre">Ordre</label>
            <input type="number" id="ordre" name="ordre" min="1" value="<?= htmlspecialchars((string) $formulaire['ordre']) ?>" required>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?= htmlspecialchars($formulaire['description'] ?? '') ?>" placeholder="Optionnel">
        </div>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter le semestre' ?></button>
        <?php if ($idEdition): ?>
            <a href="semestres.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
