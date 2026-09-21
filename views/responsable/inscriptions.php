<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$controller = new InscriptionController();
$message = "";
$erreur  = "";
$idEdition = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'valider' && isset($_POST['idInscription'])) {
        $message = $controller->validerInscription((int) $_POST['idInscription']) ? "Inscription validée avec succès." : "";
        if ($message === "") { $erreur = "Impossible de valider cette inscription."; }
    } elseif ($action === 'refuser' && isset($_POST['idInscription'])) {
        $message = $controller->refuserInscription((int) $_POST['idInscription']) ? "Inscription refusée." : "";
        if ($message === "") { $erreur = "Impossible de refuser cette inscription."; }
    } elseif ($action === 'supprimer' && isset($_POST['idInscription'])) {
        try {
            $statement = database()->prepare("DELETE FROM inscription WHERE idInscription = :id");
            $statement->execute(['id' => (int) $_POST['idInscription']]);
            $message = "Inscription supprimée avec succès.";
        } catch (PDOException $e) {
            $erreur = "Impossible de supprimer cette inscription : des paiements y sont rattachés.";
        }
    } elseif ($action === 'modifier' && isset($_POST['idInscription'])) {
        $idNiveau = (int) ($_POST['idNiveau'] ?? 0);
        $statut   = trim($_POST['statut'] ?? '');
        if ($idNiveau <= 0 || $statut === '') {
            $erreur = "Le niveau et le statut sont obligatoires.";
            $idEdition = (int) $_POST['idInscription'];
        } else {
            $statement = database()->prepare("UPDATE inscription SET idNiveau = :idNiveau, statut = :statut WHERE idInscription = :id");
            $statement->execute(['idNiveau' => $idNiveau, 'statut' => $statut, 'id' => (int) $_POST['idInscription']]);
            $message = "Inscription modifiée avec succès.";
        }
    } elseif ($action === 'ajouter') {
        $idEtudiant    = (int) ($_POST['idEtudiant'] ?? 0);
        $idNiveau      = (int) ($_POST['idNiveau'] ?? 0);
        $anneeScolaire = trim($_POST['anneeScolaire'] ?? '');
        if ($idEtudiant <= 0 || $idNiveau <= 0 || $anneeScolaire === '') {
            $erreur = "L'étudiant, le niveau et l'année scolaire sont obligatoires.";
        } else {
            $controller->demanderInscription($idEtudiant, $idNiveau, $anneeScolaire);
            $message = "Inscription créée avec succès (statut : en attente).";
        }
    }
}

$inscriptionEdition = null;
if ($idEdition) {
    $statement = database()->prepare(
        "SELECT i.idInscription, i.idNiveau, i.statut, i.dateInscription,
                d.anneeScolaire, e.matricule, u.nom, u.prenom, u.email,
                n.libelle AS niveau_libelle
         FROM inscription i
         INNER JOIN dossier_etudiant d ON i.idDossier = d.idDossier
         INNER JOIN etudiant e ON d.idEtudiant = e.idUtilisateur
         INNER JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur
         INNER JOIN niveau n ON i.idNiveau = n.idNiveau
         WHERE i.idInscription = :id"
    );
    $statement->execute(['id' => $idEdition]);
    $inscriptionEdition = $statement->fetch() ?: null;
    if (!$inscriptionEdition) {
        $idEdition = null;
    }
}

$inscriptionsEnAttente = $controller->getInscriptionsEnAttente();

$toutes = database()->query(
    "SELECT i.idInscription, i.dateInscription, i.statut,
            d.anneeScolaire, e.matricule, u.nom, u.prenom,
            n.libelle AS niveau_libelle
     FROM inscription i
     INNER JOIN dossier_etudiant d ON i.idDossier = d.idDossier
     INNER JOIN etudiant e ON d.idEtudiant = e.idUtilisateur
     INNER JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur
     INNER JOIN niveau n ON i.idNiveau = n.idNiveau
     ORDER BY i.dateInscription DESC"
)->fetchAll();

$etudiants = database()->query(
    "SELECT e.idUtilisateur, e.matricule, u.nom, u.prenom
     FROM etudiant e INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
     ORDER BY u.nom, u.prenom"
)->fetchAll();

$niveaux = database()->query(
    "SELECT n.idNiveau, n.libelle, f.libelle AS formationLibelle
     FROM niveau n INNER JOIN formation f ON f.idFormation = n.idFormation
     ORDER BY f.libelle, n.ordre"
)->fetchAll();

$pageTitle  = 'Inscriptions';
$activeMenu = 'inscriptions';
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Demandes d'inscription en attente (<?= count($inscriptionsEnAttente) ?>)</h2>

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
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Année scolaire</th>
                <th style="padding:10px; text-align:left;">Date demande</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptionsEnAttente as $ins): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($ins['nom'] . ' ' . $ins['prenom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['niveau_libelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['anneeScolaire']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['dateInscription']) ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="idInscription" value="<?= $ins['idInscription'] ?>">
                            <button type="submit" name="action" value="valider" class="btn" style="background:#2e7d32;">Valider</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="idInscription" value="<?= $ins['idInscription'] ?>">
                            <button type="submit" name="action" value="refuser" class="btn" style="background:#c62828;"
                                onclick="return confirm('Êtes-vous sûr de refuser cette inscription ?')">Refuser</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$inscriptionsEnAttente): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune demande en attente.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h2>Toutes les inscriptions (<?= count($toutes) ?>)</h2>
<div class="card" style="overflow-x:auto; margin-bottom:24px;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Année</th>
                <th style="padding:10px; text-align:left;">Statut</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($toutes as $ins): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($ins['nom'] . ' ' . $ins['prenom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['niveau_libelle']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($ins['anneeScolaire']) ?></td>
                    <td style="padding:10px;">
                        <span style="padding:2px 8px; border-radius:10px; font-size:12px; font-weight:bold;
                            background:<?= $ins['statut'] === 'VALIDEE' ? '#e6f4ea' : ($ins['statut'] === 'REFUSEE' ? '#fdeeee' : '#fff7e6') ?>;
                            color:<?= $ins['statut'] === 'VALIDEE' ? '#2e7d32' : ($ins['statut'] === 'REFUSEE' ? '#c62828' : '#b26a00') ?>;">
                            <?= htmlspecialchars($ins['statut']) ?>
                        </span>
                    </td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="?id=<?= $ins['idInscription'] ?>" class="btn" style="padding:6px 10px; font-size:12px;">Détails / Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer définitivement cette inscription ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idInscription" value="<?= $ins['idInscription'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$toutes): ?>
                <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune inscription enregistrée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($inscriptionEdition): ?>
    <div class="card" style="max-width:520px; margin-bottom:24px;">
        <h3 style="margin-top:0;">Détails de l'inscription</h3>
        <p><strong>Étudiant :</strong> <?= htmlspecialchars($inscriptionEdition['nom'] . ' ' . $inscriptionEdition['prenom']) ?></p>
        <p><strong>Matricule :</strong> <?= htmlspecialchars($inscriptionEdition['matricule']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($inscriptionEdition['email']) ?></p>
        <p><strong>Année scolaire :</strong> <?= htmlspecialchars($inscriptionEdition['anneeScolaire']) ?></p>
        <p><strong>Date de la demande :</strong> <?= htmlspecialchars($inscriptionEdition['dateInscription']) ?></p>
        <form method="POST">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="idInscription" value="<?= $inscriptionEdition['idInscription'] ?>">
            <div class="form-field">
                <label for="idNiveau">Niveau</label>
                <select id="idNiveau" name="idNiveau" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <?php foreach ($niveaux as $niveau): ?>
                        <option value="<?= $niveau['idNiveau'] ?>" <?= (int) $inscriptionEdition['idNiveau'] === (int) $niveau['idNiveau'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($niveau['formationLibelle'] . ' - ' . $niveau['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="statut">Statut</label>
                <select id="statut" name="statut" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    <?php foreach (['EN_ATTENTE', 'VALIDEE', 'REFUSEE'] as $s): ?>
                        <option value="<?= $s ?>" <?= $inscriptionEdition['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%;">Enregistrer les modifications</button>
            <a href="inscriptions.php" class="btn" style="width:100%; margin-top:8px; background:#6b7280; text-align:center; box-sizing:border-box;">Fermer</a>
        </form>
    </div>
<?php endif; ?>

<div class="card" style="max-width:480px;">
    <h3 style="margin-top:0;">Ajouter une inscription</h3>
    <form method="POST">
        <input type="hidden" name="action" value="ajouter">
        <div class="form-field">
            <label for="idEtudiant">Étudiant</label>
            <select id="idEtudiant" name="idEtudiant" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($etudiants as $etu): ?>
                    <option value="<?= $etu['idUtilisateur'] ?>"><?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom'] . ' (' . $etu['matricule'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="idNiveauAjout">Niveau</label>
            <select id="idNiveauAjout" name="idNiveau" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">Sélectionner</option>
                <?php foreach ($niveaux as $niveau): ?>
                    <option value="<?= $niveau['idNiveau'] ?>"><?= htmlspecialchars($niveau['formationLibelle'] . ' - ' . $niveau['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="anneeScolaire">Année scolaire</label>
            <input type="text" id="anneeScolaire" name="anneeScolaire" placeholder="Ex : 2025-2026" required>
        </div>
        <button type="submit" class="btn" style="width:100%;">Créer l'inscription</button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
