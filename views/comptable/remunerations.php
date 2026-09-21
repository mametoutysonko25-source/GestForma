<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
requireRole(['comptable']);
$db = database();
$message = null;
$erreur = null;
$edition = null;
$idEdition = (int) ($_GET['edit'] ?? 0);
if ($idEdition > 0) {
    $stmt = $db->prepare('SELECT * FROM remuneration_formateur WHERE idRemuneration = :id');
    $stmt->execute(['id' => $idEdition]);
    $edition = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['idRemuneration'] ?? 0);
    if ($action === 'supprimer' && $id > 0) {
        $stmt = $db->prepare('DELETE FROM remuneration_formateur WHERE idRemuneration = :id');
        $stmt->execute(['id' => $id]);
        $message = 'Rémunération supprimée.';
    } elseif ($action === 'enregistrer') {
        $idFormateur = (int) ($_POST['idFormateur'] ?? 0);
        $mois = trim($_POST['mois'] ?? '');
        $heures = (float) ($_POST['heuresValidees'] ?? 0);
        $montantDu = (float) ($_POST['montantDu'] ?? 0);
        $montantPaye = (float) ($_POST['montantPaye'] ?? 0);
        $statut = $montantPaye >= $montantDu ? 'PAYEE' : 'A_PAYER';
        if (!$idFormateur || $mois === '' || $heures < 0 || $montantDu < 0 || $montantPaye < 0) {
            $erreur = 'Tous les champs de rémunération sont obligatoires et positifs.';
        } elseif ($id > 0) {
            $stmt = $db->prepare('UPDATE remuneration_formateur SET mois = :mois, heuresValidees = :heures, montantDu = :du, montantPaye = :paye, statut = :statut, idFormateur = :formateur WHERE idRemuneration = :id');
            $stmt->execute(['id' => $id, 'mois' => $mois, 'heures' => $heures, 'du' => $montantDu, 'paye' => $montantPaye, 'statut' => $statut, 'formateur' => $idFormateur]);
            $message = 'Rémunération modifiée.';
        } else {
            $stmt = $db->prepare('INSERT INTO remuneration_formateur (mois, heuresValidees, montantDu, montantPaye, statut, idFormateur) VALUES (:mois, :heures, :du, :paye, :statut, :formateur)');
            $stmt->execute(['mois' => $mois, 'heures' => $heures, 'du' => $montantDu, 'paye' => $montantPaye, 'statut' => $statut, 'formateur' => $idFormateur]);
            $message = 'Rémunération enregistrée.';
        }
    }
}

$formateurs = $db->query('SELECT f.idUtilisateur, u.nom, u.prenom, f.matricule FROM formateur f INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur ORDER BY u.nom, u.prenom')->fetchAll();
$remunerations = $db->query('SELECT r.idRemuneration, r.mois, r.heuresValidees, r.montantDu, r.montantPaye, r.statut, f.matricule, u.nom, u.prenom FROM remuneration_formateur r INNER JOIN formateur f ON f.idUtilisateur = r.idFormateur INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur ORDER BY r.mois DESC, u.nom, u.prenom')->fetchAll();
$pageTitle = 'Rémunérations'; $showSidebar = true; require __DIR__ . '/../../includes/header.php';
?>
<h2 style="margin-top:0;">Rémunérations des formateurs</h2>
<?php if ($message): ?><p style="color:#217a4b;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($erreur): ?><p style="color:#c62828;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
<div class="card" style="max-width:760px;"><h3><?= $edition ? 'Modifier la rémunération' : 'Ajouter une rémunération' ?></h3><form method="post"><input type="hidden" name="action" value="enregistrer"><input type="hidden" name="idRemuneration" value="<?= (int) ($edition['idRemuneration'] ?? 0) ?>"><div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"><div class="form-field"><label>Formateur</label><select name="idFormateur" required><option value="">Sélectionner</option><?php foreach ($formateurs as $formateur): ?><option value="<?= (int) $formateur['idUtilisateur'] ?>" <?= ((int) ($edition['idFormateur'] ?? 0) === (int) $formateur['idUtilisateur']) ? 'selected' : '' ?>><?= htmlspecialchars($formateur['prenom'].' '.$formateur['nom'].' - '.$formateur['matricule']) ?></option><?php endforeach; ?></select></div><div class="form-field"><label>Mois</label><input type="month" name="mois" required value="<?= htmlspecialchars($edition['mois'] ?? '') ?>"></div><div class="form-field"><label>Heures validées</label><input type="number" name="heuresValidees" min="0" step="0.01" required value="<?= htmlspecialchars($edition['heuresValidees'] ?? '') ?>"></div><div class="form-field"><label>Montant dû</label><input type="number" name="montantDu" min="0" step="0.01" required value="<?= htmlspecialchars($edition['montantDu'] ?? '') ?>"></div><div class="form-field"><label>Montant payé</label><input type="number" name="montantPaye" min="0" step="0.01" required value="<?= htmlspecialchars($edition['montantPaye'] ?? '') ?>"></div></div><button class="btn" type="submit">Enregistrer</button></form></div>
<div class="card" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;"><tr><th>Formateur</th><th>Mois</th><th>Heures</th><th>Dû</th><th>Payé</th><th>Statut</th><th>Actions</th></tr><?php foreach ($remunerations as $remuneration): ?><tr><td><?= htmlspecialchars($remuneration['prenom'].' '.$remuneration['nom']) ?></td><td><?= htmlspecialchars($remuneration['mois']) ?></td><td><?= htmlspecialchars($remuneration['heuresValidees']) ?></td><td><?= number_format((float) $remuneration['montantDu'], 2, ',', ' ') ?> FCFA</td><td><?= number_format((float) $remuneration['montantPaye'], 2, ',', ' ') ?> FCFA</td><td><?= htmlspecialchars($remuneration['statut']) ?></td><td><a href="?edit=<?= (int) $remuneration['idRemuneration'] ?>">Modifier</a><form method="post" style="display:inline;margin-left:8px;" onsubmit="return confirm('Supprimer cette rémunération ?');"><input type="hidden" name="action" value="supprimer"><input type="hidden" name="idRemuneration" value="<?= (int) $remuneration['idRemuneration'] ?>"><button type="submit" style="border:0;background:none;color:#c62828;cursor:pointer;">Supprimer</button></form></td></tr><?php endforeach; ?><?php if (!$remunerations): ?><tr><td colspan="7">Aucune rémunération enregistrée.</td></tr><?php endif; ?></table></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>