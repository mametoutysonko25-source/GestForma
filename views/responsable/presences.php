<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'valider' && isset($_POST['idPresence'])) {
        $statement = database()->prepare("UPDATE presence SET validee = 1 WHERE idPresence = :id");
        $statement->execute(['id' => (int) $_POST['idPresence']]);
        $message = "Justification validée.";
    } elseif ($action === 'supprimer' && isset($_POST['idPresence'])) {
        $statement = database()->prepare("DELETE FROM presence WHERE idPresence = :id");
        $statement->execute(['id' => (int) $_POST['idPresence']]);
        $message = "Présence supprimée avec succès.";
    } elseif ($action === 'enregistrer') {
        $idPresence      = isset($_POST['idPresence']) && $_POST['idPresence'] !== '' ? (int) $_POST['idPresence'] : null;
        $idSeance        = (int) ($_POST['idSeance'] ?? 0);
        $idEtudiant      = (int) ($_POST['idEtudiant'] ?? 0);
        $statutPresence  = trim($_POST['statutPresence'] ?? '');
        $heureArrivee    = trim($_POST['heureArrivee'] ?? '');
        $justification   = trim($_POST['justification'] ?? '');

        if ($idSeance <= 0 || $idEtudiant <= 0 || $statutPresence === '') {
            $erreur = "La séance, l'étudiant et le statut sont obligatoires.";
            $idEdition = $idPresence;
        } elseif ($idPresence) {
            $statement = database()->prepare(
                "UPDATE presence SET idSeance = :idSeance, idEtudiant = :idEtudiant, statutPresence = :statutPresence,
                        heureArrivee = :heureArrivee, justification = :justification
                 WHERE idPresence = :id"
            );
            $statement->execute([
                'idSeance' => $idSeance, 'idEtudiant' => $idEtudiant, 'statutPresence' => $statutPresence,
                'heureArrivee' => $heureArrivee ?: null, 'justification' => $justification ?: null, 'id' => $idPresence,
            ]);
            $message = "Présence modifiée avec succès.";
        } else {
            try {
                $statement = database()->prepare(
                    "INSERT INTO presence (idSeance, idEtudiant, statutPresence, heureArrivee, justification)
                     VALUES (:idSeance, :idEtudiant, :statutPresence, :heureArrivee, :justification)"
                );
                $statement->execute([
                    'idSeance' => $idSeance, 'idEtudiant' => $idEtudiant, 'statutPresence' => $statutPresence,
                    'heureArrivee' => $heureArrivee ?: null, 'justification' => $justification ?: null,
                ]);
                $message = "Présence enregistrée avec succès.";
            } catch (PDOException $e) {
                $erreur = "Cet étudiant a déjà une présence enregistrée pour cette séance.";
            }
        }
    }
}

$formulaire = ['idPresence' => '', 'idSeance' => '', 'idEtudiant' => '', 'statutPresence' => 'PRESENT', 'heureArrivee' => '', 'justification' => ''];
if ($idEdition) {
    $statement = database()->prepare("SELECT * FROM presence WHERE idPresence = :id");
    $statement->execute(['id' => $idEdition]);
    $trouve = $statement->fetch();
    if ($trouve) {
        $formulaire = $trouve;
    } else {
        $idEdition = null;
    }
}

$presences = database()->query(
    "SELECT p.idPresence, p.statutPresence, p.heureArrivee, p.justification, p.validee,
            se.dateSeance, m.libelle AS moduleLibelle,
            u.nom, u.prenom, e.matricule
     FROM presence p
     INNER JOIN seance se ON se.idSeance = p.idSeance
     INNER JOIN module m ON m.idModule = se.idModule
     INNER JOIN etudiant e ON e.idUtilisateur = p.idEtudiant
     INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     ORDER BY se.dateSeance DESC
     LIMIT 150"
)->fetchAll();

$seances = database()->query(
    "SELECT sn.idSeance, sn.dateSeance, m.libelle AS moduleLibelle
     FROM seance sn INNER JOIN module m ON m.idModule = sn.idModule
     ORDER BY sn.dateSeance DESC LIMIT 200"
)->fetchAll();

$etudiants = database()->query(
    "SELECT e.idUtilisateur, e.matricule, u.nom, u.prenom
     FROM etudiant e INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     ORDER BY u.nom, u.prenom"
)->fetchAll();

$pageTitle  = 'Présences';
$activeMenu = 'presences';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Suivi des présences</h2>

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
                <th style="padding:10px; text-align:left;">Module</th>
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Statut</th>
                <th style="padding:10px; text-align:left;">Justification</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($presences as $presence): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($presence['dateSeance']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($presence['moduleLibelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($presence['prenom'] . ' ' . $presence['nom'] . ' (' . $presence['matricule'] . ')') ?></td>
                    <td style="padding:10px; font-weight:bold; color:<?= $presence['statutPresence'] === 'PRESENT' ? '#2e7d32' : '#c62828' ?>;">
                        <?= htmlspecialchars($presence['statutPresence']) ?>
                    </td>
                    <td style="padding:10px;">
                        <?= $presence['justification'] ? htmlspecialchars($presence['justification']) : '-' ?>
                        <?php if ($presence['justification']): ?>
                            <br><span style="font-size:12px; color:<?= $presence['validee'] ? '#2e7d32' : '#c62828' ?>;">
                                <?= $presence['validee'] ? 'Validée' : 'Non validée' ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px; white-space:nowrap;">
                        <?php if ($presence['justification'] && !$presence['validee']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="valider">
                                <input type="hidden" name="idPresence" value="<?= $presence['idPresence'] ?>">
                                <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#2e7d32;">Valider</button>
                            </form>
                        <?php endif; ?>
                        <a href="?id=<?= $presence['idPresence'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet enregistrement de présence ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idPresence" value="<?= $presence['idPresence'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$presences): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune présence enregistrée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;"><?= $idEdition ? 'Modifier la présence' : 'Ajouter une présence' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="enregistrer">
        <input type="hidden" name="idPresence" value="<?= htmlspecialchars((string) $formulaire['idPresence']) ?>">
        <div class="form-field">
            <label for="idSeance">Séance</label>
            <select id="idSeance" name="idSeance" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($seances as $seance): ?>
                    <option value="<?= $seance['idSeance'] ?>" <?= (int) $formulaire['idSeance'] === (int) $seance['idSeance'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($seance['dateSeance'] . ' - ' . $seance['moduleLibelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
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
            <label for="statutPresence">Statut</label>
            <select id="statutPresence" name="statutPresence" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <?php foreach (['PRESENT', 'ABSENT', 'RETARD'] as $s): ?>
                    <option value="<?= $s ?>" <?= $formulaire['statutPresence'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="heureArrivee">Heure d'arrivée</label>
            <input type="time" id="heureArrivee" name="heureArrivee" value="<?= htmlspecialchars(substr((string) $formulaire['heureArrivee'], 0, 5)) ?>">
        </div>
        <div class="form-field">
            <label for="justification">Justification (optionnel)</label>
            <input type="text" id="justification" name="justification" value="<?= htmlspecialchars((string) $formulaire['justification']) ?>">
        </div>
        <button type="submit" class="btn" style="width:100%;"><?= $idEdition ? 'Enregistrer les modifications' : 'Ajouter la présence' ?></button>
        <?php if ($idEdition): ?>
            <a href="presences.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Annuler la modification</a>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
