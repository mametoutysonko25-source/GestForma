<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

$message = "";
$erreur  = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer' && isset($_POST['idUtilisateur'])) {
    $id = (int) $_POST['idUtilisateur'];
    try {
        $statement = database()->prepare("DELETE FROM utilisateur WHERE idUtilisateur = :id");
        $statement->execute(['id' => $id]);
        $message = "Étudiant supprimé avec succès.";
    } catch (PDOException $e) {
        $erreur = "Impossible de supprimer cet étudiant : il est référencé ailleurs dans le système.";
    }
}

$recherche = trim($_GET['q'] ?? '');

$sql = "SELECT e.idUtilisateur, e.matricule, e.statutParcours, e.dateInscription,
               u.nom, u.prenom, u.email, u.telephone, u.statutCompte
        FROM etudiant e
        INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur";
$params = [];
if ($recherche !== '') {
    $sql .= " WHERE u.nom LIKE :q OR u.prenom LIKE :q OR e.matricule LIKE :q OR u.email LIKE :q";
    $params['q'] = '%' . $recherche . '%';
}
$sql .= " ORDER BY u.nom, u.prenom";

$statement = database()->prepare($sql);
$statement->execute($params);
$etudiants = $statement->fetchAll();

$pageTitle  = 'Étudiants';
$activeMenu = 'etudiants';
require __DIR__ . '/../../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <h2 style="margin:0;">Étudiants (<?= count($etudiants) ?>)</h2>
    <a href="<?= htmlspecialchars(BASE_URL . 'views/responsable/etudiant_form.php') ?>" class="btn">+ Ajouter</a>
</div>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<form method="get" class="card" style="margin-bottom:16px; display:flex; gap:10px; align-items:flex-end; max-width:420px;">
    <div style="flex:1;">
        <label for="q" style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Rechercher</label>
        <input type="text" id="q" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Nom, prénom, matricule, email" style="width:100%; padding:8px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px;">
    </div>
    <button type="submit" class="btn">Filtrer</button>
</form>

<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Email</th>
                <th style="padding:10px; text-align:left;">Téléphone</th>
                <th style="padding:10px; text-align:left;">Statut parcours</th>
                <th style="padding:10px; text-align:left;">Compte</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($etudiants as $etu): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($etu['matricule']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($etu['email']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($etu['telephone'] ?: '-') ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($etu['statutParcours'] ?: '-') ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($etu['statutCompte']) ?></td>
                    <td style="padding:10px; white-space:nowrap;">
                        <a href="<?= htmlspecialchars(BASE_URL . 'views/responsable/etudiant_form.php?id=' . $etu['idUtilisateur']) ?>" class="btn" style="padding:6px 10px; font-size:12px;">Modifier</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer définitivement cet étudiant ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idUtilisateur" value="<?= $etu['idUtilisateur'] ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background:#c62828;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$etudiants): ?>
                <tr><td colspan="7" style="padding:16px; text-align:center; color:var(--muted);">Aucun étudiant trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
