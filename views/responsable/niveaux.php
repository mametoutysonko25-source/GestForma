<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idNiveau'])) {
        try {
            $statement = database()->prepare("DELETE FROM niveau WHERE idNiveau = :id");
            $statement->execute(['id' => (int) $_POST['idNiveau']]);
            $message = "Niveau supprimé avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer ce niveau : des semestres, modules ou inscriptions y sont encore rattachés.";
        }
    } elseif ($action === 'enregistrer') {
        $idNiveau    = isset($_POST['idNiveau']) && $_POST['idNiveau'] !== '' ? (int) $_POST['idNiveau'] : null;
        $idFormation = (int) ($_POST['idFormation'] ?? 0);
        $libelle     = trim($_POST['libelle'] ?? '');
        $ordre       = (int) ($_POST['ordre'] ?? 1);

        if ($idFormation <= 0 || $libelle === '') {
            $erreur = "La formation et le libellé du niveau sont obligatoires.";
            $idEdition = $idNiveau;
        } elseif ($idNiveau) {
            $statement = database()->prepare(
                "UPDATE niveau SET idFormation = :idFormation, libelle = :libelle, ordre = :ordre WHERE idNiveau = :id"
            );
            $statement->execute(['idFormation' => $idFormation, 'libelle' => $libelle, 'ordre' => $ordre, 'id' => $idNiveau]);
            $message = "Niveau « " . $libelle . " » modifié avec succès.";
        } else {
            $statement = database()->prepare(
                "INSERT INTO niveau (idFormation, libelle, ordre) VALUES (:idFormation, :libelle, :ordre)"
            );
            $statement->execute(['idFormation' => $idFormation, 'libelle' => $libelle, 'ordre' => $ordre]);
            $message = "Niveau « " . $libelle . " » ajouté avec succès.";
        }
    }
}

$formulaire = ['idNiveau' => '', 'idFormation' => '', 'libelle' => '', 'ordre' => 1];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM niveau WHERE idNiveau = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$niveaux = database()->query(
    "SELECT n.idNiveau, n.libelle, n.ordre, f.libelle AS formationLibelle,
            COUNT(s.idSemestre) AS nbSemestres
     FROM niveau n
     INNER JOIN formation f ON f.idFormation = n.idFormation
     LEFT JOIN semestre s ON s.idNiveau = n.idNiveau
     GROUP BY n.idNiveau, n.libelle, n.ordre, f.libelle
     ORDER BY f.libelle, n.ordre"
)->fetchAll();

$formations = database()->query("SELECT idFormation, libelle FROM formation ORDER BY libelle")->fetchAll();

$pageTitle  = 'Niveaux';
$activeMenu = 'niveaux';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Niveaux (<?= count($niveaux) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Ordre</th>
                <th style="padding:10px; text-align:left;">Semestres</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($niveaux as $niveau): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($niveau['formationLibelle']) ?></td>
                    <td style="padding:10px;"><strong><?= htmlspecialchars($niveau['libelle']) ?></strong></td>
                    <td style="padding:10px;"><?= (int) $niveau['ordre'] ?></td>
                    <td style="padding:10px;"><?= (int) $niveau['nbSemestres'] ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $niveau['idNiveau'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce niveau ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idNiveau" value="<?= $niveau['idNiveau'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$niveaux): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucun niveau enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier le niveau' : 'Ajouter un niveau' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idNiveau" value="<?= htmlspecialchars((string) $formulaire['idNiveau']) ?>">
        <div class="form-field">
            <label for="idFormation">Formation</label>
            <select id="idFormation" name="idFormation" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($formations as $formation): ?>
                    <option value="<?= $formation['idFormation'] ?>" <?= (int) $formulaire['idFormation'] === (int) $formation['idFormation'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($formation['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="libelle">Libellé du niveau</label>
            <input type="text" id="libelle" name="libelle" value="<?= htmlspecialchars($formulaire['libelle']) ?>" required placeholder="Ex : Licence 1">
        </div>
        <div class="form-field">
            <label for="ordre">Ordre</label>
            <input type="number" id="ordre" name="ordre" min="1" value="<?= htmlspecialchars((string) $formulaire['ordre']) ?>" required>
        </div>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter le niveau' ?></button>
        <?php if ($idEdition): ?>
            <a href="niveaux.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
