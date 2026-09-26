<?php
require_once __DIR__ . '/../controllers/helpers.php';
$currentUser = requireRole(['administrateur']);
$db = database();
$id = (int) ($_GET['id'] ?? 0);
$edition = $id > 0;
$utilisateur = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '', 'nomUtilisateur' => '', 'role' => 'formateur'];
$identifiantsGeneres = null;

function genererIdentifiantAdmin(PDO $db, string $prenom, string $nom): string
{
    $base = strtolower(trim($prenom) . '.' . trim($nom));
    $base = preg_replace('/[^a-z0-9.]+/', '', strtr($base, [
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'â' => 'a',
        'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ù' => 'u', 'ç' => 'c',
    ]));
    $base = $base !== '' ? $base : 'utilisateur';
    $candidat = $base;
    $suffixe = 1;
    $requete = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE nomUtilisateur = :nomUtilisateur');

    while (true) {
        $requete->execute(['nomUtilisateur' => $candidat]);
        if ((int) $requete->fetchColumn() === 0) {
            return $candidat;
        }
        $suffixe++;
        $candidat = $base . $suffixe;
    }
}

function genererMotDePasseAdmin(): string
{
    return 'Adm' . random_int(1000, 9999) . '!';
}

function genererMatriculeAdmin(string $role, int $idUtilisateur): ?string
{
    $prefixes = ['etudiant' => 'Etu', 'formateur' => 'FOR', 'responsable' => 'RP'];
    return isset($prefixes[$role]) ? $prefixes[$role] . str_pad((string) $idUtilisateur, 6, '0', STR_PAD_LEFT) : null;
}

if ($edition) {
    $requete = $db->prepare('SELECT u.*, CASE WHEN a.idUtilisateur IS NOT NULL THEN "administrateur" WHEN d.idUtilisateur IS NOT NULL THEN "directeur" WHEN r.idUtilisateur IS NOT NULL THEN "responsable" WHEN f.idUtilisateur IS NOT NULL THEN "formateur" WHEN c.idUtilisateur IS NOT NULL THEN "comptable" WHEN e.idUtilisateur IS NOT NULL THEN "etudiant" ELSE "" END AS role FROM utilisateur u LEFT JOIN administrateur a ON a.idUtilisateur=u.idUtilisateur LEFT JOIN directeur d ON d.idUtilisateur=u.idUtilisateur LEFT JOIN responsable_pedagogique r ON r.idUtilisateur=u.idUtilisateur LEFT JOIN formateur f ON f.idUtilisateur=u.idUtilisateur LEFT JOIN comptable c ON c.idUtilisateur=u.idUtilisateur LEFT JOIN etudiant e ON e.idUtilisateur=u.idUtilisateur WHERE u.idUtilisateur=:id');
    $utilisateur = $requete->execute(['id' => $id]) ? ($requete->fetch() ?: $utilisateur) : $utilisateur;
}
$erreur = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? ''); $prenom = trim($_POST['prenom'] ?? ''); $email = trim($_POST['email'] ?? ''); $telephone = trim($_POST['telephone'] ?? ''); $role = $_POST['role'] ?? '';
    if ($nom === '' || $prenom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || (!$edition && !in_array($role, ['administrateur','directeur','responsable','formateur','comptable','etudiant'], true))) { $erreur = 'Le nom, le prénom, un e-mail valide et un rôle sont obligatoires.'; }
    else try {
        if ($edition) { $requete = $db->prepare('UPDATE utilisateur SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone WHERE idUtilisateur=:id'); $requete->execute(compact('nom','prenom','email','telephone','id')); journaliserAction('Modification utilisateur', 'Utilisateur #' . $id); flashMessage('success', 'Utilisateur modifié avec succès.'); }
        else { $identifiant = genererIdentifiantAdmin($db, $prenom, $nom); $motDePasse = genererMotDePasseAdmin(); $db->beginTransaction(); $requete = $db->prepare('INSERT INTO utilisateur (nom,prenom,email,telephone,nomUtilisateur,motDePasseHash) VALUES (:nom,:prenom,:email,:telephone,:identifiant,:motDePasse)'); $requete->execute(['nom'=>$nom,'prenom'=>$prenom,'email'=>$email,'telephone'=>$telephone,'identifiant'=>$identifiant,'motDePasse'=>password_hash($motDePasse, PASSWORD_DEFAULT)]); $nouvelId = (int) $db->lastInsertId(); $tables = ['administrateur'=>'administrateur','directeur'=>'directeur','responsable'=>'responsable_pedagogique','formateur'=>'formateur','comptable'=>'comptable','etudiant'=>'etudiant']; $matricule = genererMatriculeAdmin($role, $nouvelId); $colonnes = $matricule !== null ? 'idUtilisateur, matricule' : 'idUtilisateur'; $parametres = $matricule !== null ? ':id, :matricule' : ':id'; $requete = $db->prepare('INSERT INTO ' . $tables[$role] . ' (' . $colonnes . ') VALUES (' . $parametres . ')'); $requete->execute($matricule !== null ? ['id'=>$nouvelId,'matricule'=>$matricule] : ['id'=>$nouvelId]); $db->commit(); $identifiantsGeneres = ['nomUtilisateur'=>$identifiant,'motDePasse'=>$motDePasse,'matricule'=>$matricule]; journaliserAction('Création utilisateur', $prenom . ' ' . $nom . ' (' . $role . ')'); flashMessage('success', 'Utilisateur ajouté avec succès.'); }
        if ($edition) { header('Location: ' . BASE_URL . 'admin/utilisateurs.php'); exit; }
    } catch (Throwable $exception) { if ($db->inTransaction()) $db->rollBack(); $erreur = 'Impossible d’enregistrer ce compte : e-mail ou identifiant déjà utilisé.'; }
}
$pageTitle = $edition ? 'Modifier un utilisateur' : 'Ajouter un utilisateur'; $contentClass = 'management-content'; require __DIR__ . '/../includes/header.php';
?>
<?php if ($identifiantsGeneres): ?><div class="card" style="border-left:4px solid #1e3a8a; margin-bottom:16px;"><strong>Identifiants de connexion générés</strong> — à communiquer au nouvel utilisateur :<br>Identifiant : <code><?= htmlspecialchars($identifiantsGeneres['nomUtilisateur']) ?></code> — Mot de passe temporaire : <code><?= htmlspecialchars($identifiantsGeneres['motDePasse']) ?></code><?php if ($identifiantsGeneres['matricule']): ?><br>Matricule : <code><?= htmlspecialchars($identifiantsGeneres['matricule']) ?></code><?php endif; ?><br><span style="font-size:12px; color:var(--muted);">La connexion se fait avec l'e-mail de l'utilisateur, pas cet identifiant. Le mot de passe temporaire lui sera nécessaire pour se connecter.</span></div><?php endif; ?>
<div class="card form-card"><div class="section-heading"><div><h2><?= htmlspecialchars($pageTitle) ?></h2><p>Renseignez les informations du compte avec soin.</p></div></div><?php if ($erreur): ?><p class="form-error"><?= htmlspecialchars($erreur) ?></p><?php endif; ?><form method="post"><div class="form-grid"><label>Nom<input name="nom" required value="<?= htmlspecialchars($utilisateur['nom']) ?>"></label><label>Prénom<input name="prenom" required value="<?= htmlspecialchars($utilisateur['prenom']) ?>"></label><label>E-mail<input type="email" name="email" required value="<?= htmlspecialchars($utilisateur['email']) ?>"></label><label>Téléphone<input name="telephone" value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>"></label><label>Rôle<select name="role" <?= $edition ? 'disabled' : '' ?>><option value="formateur" <?= $utilisateur['role']==='formateur'?'selected':'' ?>>Formateur</option><option value="responsable" <?= $utilisateur['role']==='responsable'?'selected':'' ?>>Responsable pédagogique</option><option value="comptable" <?= $utilisateur['role']==='comptable'?'selected':'' ?>>Comptable</option><option value="administrateur" <?= $utilisateur['role']==='administrateur'?'selected':'' ?>>Administrateur</option><option value="directeur" <?= $utilisateur['role']==='directeur'?'selected':'' ?>>Directeur</option><option value="etudiant" <?= $utilisateur['role']==='etudiant'?'selected':'' ?>>Étudiant</option></select></label></div><div class="form-actions"><button class="btn btn-primary" type="submit">Enregistrer</button><a class="btn btn-secondary" href="<?= htmlspecialchars(BASE_URL . 'admin/utilisateurs.php') ?>">Annuler</a></div></form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>