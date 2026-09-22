<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && isset($_POST['idResultat'])) {
        $statement = database()->prepare("DELETE FROM resultat WHERE idResultat = :id");
        $statement->execute(['id' => (int) $_POST['idResultat']]);
        $message = "Résultat supprimé avec succès.";
    } elseif ($action === 'ajouter_evaluation') {
        $idModule = (int) ($_POST['idModule'] ?? 0);
        $titre    = trim($_POST['titreEvaluation'] ?? '');
        $dateLimite = trim($_POST['dateLimite'] ?? '');
        if ($idModule <= 0 || $titre === '' || $dateLimite === '') {
            $erreur = "Le module, le titre et la date limite de l'évaluation sont obligatoires.";
        } else {
            $statement = database()->prepare(
                "INSERT INTO evaluation (idModule, titre, dateLimite) VALUES (:idModule, :titre, :dateLimite)"
            );
            $statement->execute(['idModule' => $idModule, 'titre' => $titre, 'dateLimite' => $dateLimite]);
            $message = "Évaluation « " . $titre . " » créée avec succès.";
        }
    } elseif ($action === 'enregistrer') {
        $idResultat   = isset($_POST['idResultat']) && $_POST['idResultat'] !== '' ? (int) $_POST['idResultat'] : null;
        $idEvaluation = (int) ($_POST['idEvaluation'] ?? 0);
        $idEtudiant   = (int) ($_POST['idEtudiant'] ?? 0);
        $note         = $_POST['note'] !== '' ? (float) $_POST['note'] : null;
        $appreciation = trim($_POST['appreciation'] ?? '');

        if ($idEvaluation <= 0 || $idEtudiant <= 0) {
            $erreur = "L'évaluation et l'étudiant sont obligatoires.";
            $idEdition = $idResultat;
        } elseif ($idResultat) {
            $statement = database()->prepare(
                "UPDATE resultat SET idEvaluation = :idEvaluation, idEtudiant = :idEtudiant, note = :note,
                        appreciation = :appreciation, datePublication = NOW()
                 WHERE idResultat = :id"
            );
            $statement->execute([
                'idEvaluation' => $idEvaluation, 'idEtudiant' => $idEtudiant, 'note' => $note,
                'appreciation' => $appreciation ?: null, 'id' => $idResultat,
            ]);
            $message = "Résultat modifié avec succès.";
        } else {
            try {
                $statement = database()->prepare(
                    "INSERT INTO resultat (idEvaluation, idEtudiant, note, appreciation, datePublication)
                     VALUES (:idEvaluation, :idEtudiant, :note, :appreciation, NOW())"
                );
                $statement->execute([
                    'idEvaluation' => $idEvaluation, 'idEtudiant' => $idEtudiant, 'note' => $note,
                    'appreciation' => $appreciation ?: null,
                ]);
                $message = "Résultat ajouté avec succès.";
            } catch (PDOException $e) {
                $erreur = "Un résultat existe déjà pour cet étudiant sur cette évaluation.";
            }
        }
    }
}

$formulaire = ['idResultat' => '', 'idEvaluation' => '', 'idEtudiant' => '', 'note' => '', 'appreciation' => ''];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM resultat WHERE idResultat = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$resultats = database()->query(
    "SELECT r.idResultat, r.note, r.appreciation, r.datePublication,
            ev.titre AS evaluationTitre, m.libelle AS moduleLibelle,
            u.nom, u.prenom, e.matricule
     FROM resultat r
     INNER JOIN evaluation ev ON ev.idEvaluation = r.idEvaluation
     INNER JOIN module m ON m.idModule = ev.idModule
     INNER JOIN etudiant e ON e.idUtilisateur = r.idEtudiant
     INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     ORDER BY r.datePublication DESC, u.nom
     LIMIT 150"
)->fetchAll();

$evaluations = database()->query(
    "SELECT ev.idEvaluation, ev.titre, ev.dateLimite, m.libelle AS moduleLibelle
     FROM evaluation ev INNER JOIN module m ON m.idModule = ev.idModule
     ORDER BY ev.dateLimite DESC"
)->fetchAll();

$modules = database()->query("SELECT idModule, libelle FROM module ORDER BY libelle")->fetchAll();

$etudiants = database()->query(
    "SELECT e.idUtilisateur, e.matricule, u.nom, u.prenom
     FROM etudiant e INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     ORDER BY u.nom, u.prenom"
)->fetchAll();

$pageTitle  = 'Résultats';
$activeMenu = 'resultats';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Résultats des évaluations (<?= count($resultats) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Module</th>
                <th style="padding:10px; text-align:left;">Évaluation</th>
                <th style="padding:10px; text-align:left;">Note</th>
                <th style="padding:10px; text-align:left;">Publication</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultats as $resultat): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($resultat['prenom'] . ' ' . $resultat['nom'] . ' (' . $resultat['matricule'] . ')') ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($resultat['moduleLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($resultat['evaluationTitre']) ?></td>
                    <td style="padding:10px; font-weight:bold;">
                        <?= $resultat['note'] !== null ? number_format((float) $resultat['note'], 2, ',', ' ') . ' / 20' : 'Non noté' ?>
                    </td>
                    <td style="padding:10px;"><?= $resultat['datePublication'] ? htmlspecialchars($resultat['datePublication']) : '-' ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $resultat['idResultat'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Détails / Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce résultat ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idResultat" value="<?= $resultat['idResultat'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$resultats): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucun résultat publié.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    <div class="card">
        <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier le résultat' : 'Ajouter un résultat' ?></h3>
        <form method="POST">
            <input type="hidden" name="action" value="enregistrer">
            <input type="hidden" name="idResultat" value="<?= htmlspecialchars((string) $formulaire['idResultat']) ?>">
            <div class="form-field">
                <label for="idEvaluation">Évaluation</label>
                <select id="idEvaluation" name="idEvaluation" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="">Sélectionner</option>
                    <?php foreach ($evaluations as $ev): ?>
                        <option value="<?= $ev['idEvaluation'] ?>" <?= (int) $formulaire['idEvaluation'] === (int) $ev['idEvaluation'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ev['moduleLibelle'] . ' - ' . $ev['titre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$evaluations): ?>
                    <p style="font-size:12px; color:#c62828; margin:4px 0 0;">Aucune évaluation n'existe encore : créez-en une ci-contre.</p>
                <?php endif; ?>
            </div>
            <div class="form-field">
                <label for="idEtudiant">Étudiant</label>
                <select id="idEtudiant" name="idEtudiant" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="">Sélectionner</option>
                    <?php foreach ($etudiants as $etu): ?>
                        <option value="<?= $etu['idUtilisateur'] ?>" <?= (int) $formulaire['idEtudiant'] === (int) $etu['idUtilisateur'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom'] . ' (' . $etu['matricule'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="note">Note (/20)</label>
                <input type="number" id="note" name="note" min="0" max="20" step="0.01" value="<?= htmlspecialchars((string) $formulaire['note']) ?>">
            </div>
            <div class="form-field">
                <label for="appreciation">Appréciation</label>
                <input type="text" id="appreciation" name="appreciation" value="<?= htmlspecialchars((string) $formulaire['appreciation']) ?>">
            </div>
            <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter le résultat' ?></button>
            <?php if ($idEdition): ?>
                <a href="resultats.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Créer une évaluation</h3>
        <p style="font-size:13px; color:var(--muted); margin-top:-8px;">Une évaluation est nécessaire avant de pouvoir y rattacher des notes.</p>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter_evaluation">
            <div class="form-field">
                <label for="idModuleEval">Module</label>
                <select id="idModuleEval" name="idModule" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="">Sélectionner</option>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?= $module['idModule'] ?>"><?= htmlspecialchars($module['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="titreEvaluation">Titre de l'évaluation</label>
                <input type="text" id="titreEvaluation" name="titreEvaluation" placeholder="Ex : Examen final" required>
            </div>
            <div class="form-field">
                <label for="dateLimite">Date limite</label>
                <input type="datetime-local" id="dateLimite" name="dateLimite" required>
            </div>
            <button type="submit" class="btn" style="width:100%;">Créer l'évaluation</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
