<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'affecter') {
    $idModule    = (int) ($_POST['idModule'] ?? 0);
    $idFormateur = $_POST['idFormateur'] !== '' ? (int) $_POST['idFormateur'] : null;

    if ($idModule <= 0) {
        $erreur = "Veuillez sélectionner un module.";
    } else {
        $statement = database()->prepare("UPDATE module SET idFormateur = :idFormateur WHERE idModule = :idModule");
        $statement->execute(['idFormateur' => $idFormateur, 'idModule' => $idModule]);
        $message = $idFormateur
            ? "Formateur affecté au module avec succès."
            : "Le module a été désaffecté.";
    }
}

$modules = database()->query(
    "SELECT m.idModule, m.libelle, m.idFormateur,
            s.libelle AS semestreLibelle, n.libelle AS niveauLibelle,
            u.nom AS formateurNom, u.prenom AS formateurPrenom
     FROM module m
     INNER JOIN semestre s ON s.idSemestre = m.idSemestre
     INNER JOIN niveau n ON n.idNiveau = s.idNiveau
     LEFT JOIN formateur f ON f.idUtilisateur = m.idFormateur
     LEFT JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
     ORDER BY n.libelle, s.libelle, m.libelle"
)->fetchAll();

$formateurs = database()->query(
    "SELECT f.idUtilisateur, f.specialite, u.nom, u.prenom
     FROM formateur f
     INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
     ORDER BY u.nom, u.prenom"
)->fetchAll();

$pageTitle  = 'Affectations';
$activeMenu = 'affectation';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Affectation des formateurs aux modules</h2>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Niveau / Semestre</th>
                <th style="padding:10px; text-align:left;">Module</th>
                <th style="padding:10px; text-align:left;">Formateur actuel</th>
                <th style="padding:10px; text-align:left;">Affecter à</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $module): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($module['niveauLibelle'] . ' - ' . $module['semestreLibelle']) ?></td>
                    <td style="padding:10px;"><strong><?= htmlspecialchars($module['libelle']) ?></strong></td>
                    <td style="padding:10px;">
                        <?php if ($module['formateurNom']): ?>
                            <?= htmlspecialchars($module['formateurPrenom'] . ' ' . $module['formateurNom']) ?>
                        <?php else: ?>
                            <span style="color:#c62828;">Non affecté</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px;">
                        <form method="POST" style="display:flex; gap:8px;">
                            <input type="hidden" name="action" value="affecter">
                            <input type="hidden" name="idModule" value="<?= $module['idModule'] ?>">
                            <select name="idFormateur" style="padding:6px; border:1px solid #d1d5db; border-radius:6px;">
                                <option value="">— Aucun —</option>
                                <?php foreach ($formateurs as $formateur): ?>
                                    <option value="<?= $formateur['idUtilisateur'] ?>" <?= (int) $module['idFormateur'] === (int) $formateur['idUtilisateur'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom'] . ($formateur['specialite'] ? ' (' . $formateur['specialite'] . ')' : '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn">Enregistrer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$modules): ?>
                <tr><td colspan="4" style="padding:16px; text-align:center; color:var(--muted);">Aucun module enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
