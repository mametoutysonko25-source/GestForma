<?php
require_once __DIR__ . '/../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);
$db = database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'statut') {
    $id = (int) ($_POST['idUtilisateur'] ?? 0);
    $statut = $_POST['statut'] ?? '';
    if ($id > 0 && in_array($statut, ['ACTIF', 'INACTIF', 'BLOQUE'], true) && $id !== (int) $currentUser['id']) {
        $requete = $db->prepare('UPDATE utilisateur SET statutCompte = :statut WHERE idUtilisateur = :id');
        $requete->execute(['statut' => $statut, 'id' => $id]);
        journaliserAction('Modification du statut utilisateur', 'Utilisateur #' . $id . ' : ' . $statut);
        flashMessage('success', 'Le statut du compte a été mis à jour.');
    } else {
        flashMessage('error', 'Le compte courant ne peut pas être désactivé ou les données sont invalides.');
    }
    header('Location: ' . BASE_URL . 'admin/utilisateurs.php');
    exit;
}

$utilisateurs = $db->query(
    "SELECT u.idUtilisateur, u.prenom, u.nom, u.email, u.telephone, u.nomUtilisateur, u.statutCompte, u.derniereConnexion,
        CASE
            WHEN a.idUtilisateur IS NOT NULL THEN 'Administrateur'
            WHEN d.idUtilisateur IS NOT NULL THEN 'Directeur'
            WHEN r.idUtilisateur IS NOT NULL THEN 'Responsable pédagogique'
            WHEN f.idUtilisateur IS NOT NULL THEN 'Formateur'
            WHEN c.idUtilisateur IS NOT NULL THEN 'Comptable'
            WHEN e.idUtilisateur IS NOT NULL THEN 'Étudiant'
            ELSE 'Sans rôle'
        END AS role
     FROM utilisateur u
     LEFT JOIN administrateur a ON a.idUtilisateur = u.idUtilisateur
     LEFT JOIN directeur d ON d.idUtilisateur = u.idUtilisateur
     LEFT JOIN responsable_pedagogique r ON r.idUtilisateur = u.idUtilisateur
     LEFT JOIN formateur f ON f.idUtilisateur = u.idUtilisateur
     LEFT JOIN comptable c ON c.idUtilisateur = u.idUtilisateur
     LEFT JOIN etudiant e ON e.idUtilisateur = u.idUtilisateur
     ORDER BY u.nom, u.prenom"
)->fetchAll();
$pageTitle = 'Utilisateurs';
$activeMenu = 'utilisateurs';
$contentClass = 'management-content';
require __DIR__ . '/../includes/header.php';
?>
<div class="director-heading"><div><h2>Utilisateurs</h2><p><?= count($utilisateurs) ?> comptes réels enregistrés.</p></div><a class="btn btn-primary" href="<?= htmlspecialchars(BASE_URL . 'admin/utilisateur_form.php') ?>">Ajouter un utilisateur</a></div>
<div class="card table-card"><table class="director-table"><thead><tr><th>Nom</th><th>Identifiant</th><th>Rôle</th><th>E-mail</th><th>Dernière connexion</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($utilisateurs as $utilisateur): ?><tr>
<td><strong><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong></td><td><?= htmlspecialchars($utilisateur['nomUtilisateur']) ?></td><td><span class="role-badge"><?= htmlspecialchars($utilisateur['role']) ?></span></td><td><?= htmlspecialchars($utilisateur['email']) ?></td><td><?= $utilisateur['derniereConnexion'] ? htmlspecialchars($utilisateur['derniereConnexion']) : '<span style="color:var(--muted);">Jamais</span>' ?></td><td><span class="status-badge status-<?= strtolower($utilisateur['statutCompte']) ?>"><?= htmlspecialchars($utilisateur['statutCompte']) ?></span></td><td class="actions-cell">
<a class="btn btn-small btn-secondary" href="<?= htmlspecialchars(BASE_URL . 'admin/utilisateur_form.php?id=' . $utilisateur['idUtilisateur']) ?>">Modifier</a>
<?php if ((int) $utilisateur['idUtilisateur'] !== (int) $currentUser['id']): ?><form method="post" class="inline-form"><input type="hidden" name="action" value="statut"><input type="hidden" name="idUtilisateur" value="<?= (int) $utilisateur['idUtilisateur'] ?>"><input type="hidden" name="statut" value="<?= $utilisateur['statutCompte'] === 'ACTIF' ? 'INACTIF' : 'ACTIF' ?>"><button class="btn btn-small <?= $utilisateur['statutCompte'] === 'ACTIF' ? 'btn-danger' : 'btn-success' ?>" type="submit"><?= $utilisateur['statutCompte'] === 'ACTIF' ? 'Désactiver' : 'Activer' ?></button></form><?php endif; ?>
</td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>