<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idFormation'])) {
        try {
            $statement = database()->prepare("DELETE FROM formation WHERE idFormation = :id");
            $statement->execute(['id' => (int) $_POST['idFormation']]);
            $message = "Formation supprimée avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer cette formation : des niveaux y sont encore rattachés.";
        }
    } elseif ($action === 'enregistrer') {
        $idFormation = isset($_POST['idFormation']) && $_POST['idFormation'] !== '' ? (int) $_POST['idFormation'] : null;
        $libelle     = trim($_POST['libelle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duree       = $_POST['duree'] !== '' ? (int) $_POST['duree'] : null;

        if ($libelle === '') {
            $erreur = "Le libellé de la formation est obligatoire.";
            $idEdition = $idFormation;
        } elseif ($idFormation) {
            $statement = database()->prepare(
                "UPDATE formation SET libelle = :libelle, description = :description, duree = :duree WHERE idFormation = :id"
            );
            $statement->execute(['libelle' => $libelle, 'description' => $description ?: null, 'duree' => $duree, 'id' => $idFormation]);
            $message = "Formation « " . $libelle . " » modifiée avec succès.";
        } else {
            $statement = database()->prepare(
                "INSERT INTO formation (libelle, description, duree) VALUES (:libelle, :description, :duree)"
            );
            $statement->execute(['libelle' => $libelle, 'description' => $description ?: null, 'duree' => $duree]);
            $message = "Formation « " . $libelle . " » ajoutée avec succès.";
        }
    }
}

$formulaire = ['idFormation' => '', 'libelle' => '', 'description' => '', 'duree' => ''];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM formation WHERE idFormation = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$formations = database()->query(
    "SELECT f.idFormation, f.libelle, f.description, f.duree,
            COUNT(n.idNiveau) AS nbNiveaux
     FROM formation f
     LEFT JOIN niveau n ON n.idFormation = f.idFormation
     GROUP BY f.idFormation, f.libelle, f.description, f.duree
     ORDER BY f.libelle"
)->fetchAll();

$pageTitle  = 'Formations';
$activeMenu = 'formations';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Formations (<?= count($formations) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Description</th>
                <th style="padding:10px; text-align:left;">Durée (mois)</th>
                <th style="padding:10px; text-align:left;">Niveaux</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formations as $formation): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><strong><?= htmlspecialchars($formation['libelle']) ?></strong></td>
                    <td style="padding:10px;"><?= htmlspecialchars($formation['description'] ?: '-') ?></td>
                    <td style="padding:10px;"><?= $formation['duree'] !== null ? htmlspecialchars($formation['duree']) : '-' ?></td>
                    <td style="padding:10px;"><?= (int) $formation['nbNiveaux'] ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $formation['idFormation'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette formation ? Les niveaux associés doivent être supprimés au préalable.');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idFormation" value="<?= $formation['idFormation'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$formations): ?>
                <tr><td colspan="5" style="padding:16px; text-align:center; color:var(--muted);">Aucune formation enregistrée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier la formation' : 'Ajouter une formation' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idFormation" value="<?= htmlspecialchars((string) $formulaire['idFormation']) ?>">
        <div class="form-field">
            <label for="libelle">Libellé</label>
            <input type="text" id="libelle" name="libelle" value="<?= htmlspecialchars($formulaire['libelle']) ?>" required>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?= htmlspecialchars($formulaire['description'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label for="duree">Durée (en mois)</label>
            <input type="number" id="duree" name="duree" min="1" value="<?= htmlspecialchars((string) ($formulaire['duree'] ?? '')) ?>">
        </div>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter la formation' ?></button>
        <?php if ($idEdition): ?>
            <a href="formations.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
