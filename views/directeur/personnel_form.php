<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['directeur']);
$db = database();
$id = (int) ($_GET['id'] ?? 0);
$edition = $id > 0;
$personnel = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '', 'role' => 'formateur'];
$roles = ['directeur' => 'Directeur', 'responsable' => 'Responsable pédagogique', 'formateur' => 'Formateur', 'comptable' => 'Comptable'];
$identifiantsGeneres = null;

function genererIdentifiantPersonnel(PDO $db, string $prenom, string $nom): string
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

function genererMotDePasseTemporairePersonnel(): string
{
    return 'Per' . random_int(1000, 9999) . '!';
}

if ($edition) {
    $requete = $db->prepare("SELECT u.*, CASE WHEN d.idUtilisateur IS NOT NULL THEN 'directeur' WHEN r.idUtilisateur IS NOT NULL THEN 'responsable' WHEN f.idUtilisateur IS NOT NULL THEN 'formateur' WHEN c.idUtilisateur IS NOT NULL THEN 'comptable' ELSE '' END AS role FROM utilisateur u LEFT JOIN directeur d ON d.idUtilisateur=u.idUtilisateur LEFT JOIN responsable_pedagogique r ON r.idUtilisateur=u.idUtilisateur LEFT JOIN formateur f ON f.idUtilisateur=u.idUtilisateur LEFT JOIN comptable c ON c.idUtilisateur=u.idUtilisateur WHERE u.idUtilisateur=:id");
    $requete->execute(['id' => $id]);
    $personnel = $requete->fetch() ?: $personnel;
}
$erreur = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? ''); $prenom = trim($_POST['prenom'] ?? ''); $email = trim($_POST['email'] ?? ''); $telephone = trim($_POST['telephone'] ?? ''); $role = $_POST['role'] ?? '';
    if ($nom === '' || $prenom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || (!$edition && !isset($roles[$role]))) {
        $erreur = 'Le nom, le prénom, un e-mail valide et le rôle sont obligatoires.';
    } else try {
        if ($edition) {
            $requete = $db->prepare('UPDATE utilisateur SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone WHERE idUtilisateur=:id');
            $requete->execute(['nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email, 'telephone'=>$telephone, 'id'=>$id]);
            journaliserAction('Modification d’un membre du personnel', 'Utilisateur #' . $id);
            flashMessage('success', 'Le membre du personnel a été modifié.');
        } else {
            $identifiant = genererIdentifiantPersonnel($db, $prenom, $nom);
            $motDePasse = genererMotDePasseTemporairePersonnel();
            $db->beginTransaction();
            $requete = $db->prepare('INSERT INTO utilisateur (nom,prenom,email,telephone,nomUtilisateur,motDePasseHash) VALUES (:nom,:prenom,:email,:telephone,:identifiant,:motDePasse)');
            $requete->execute(['nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email, 'telephone'=>$telephone, 'identifiant'=>$identifiant, 'motDePasse'=>password_hash($motDePasse, PASSWORD_DEFAULT)]);
            $nouvelId = (int) $db->lastInsertId();
            $table = $role === 'responsable' ? 'responsable_pedagogique' : $role;
            if (in_array($role, ['formateur', 'responsable'], true)) {
                $matricule = strtoupper(($role === 'formateur' ? 'FOR' : 'RP') . str_pad((string) $nouvelId, 3, '0', STR_PAD_LEFT));
                $requete = $db->prepare('INSERT INTO ' . $table . ' (idUtilisateur, matricule) VALUES (:id, :matricule)');
                $requete->execute(['id'=>$nouvelId, 'matricule'=>$matricule]);
            } else {
                $requete = $db->prepare('INSERT INTO ' . $table . ' (idUtilisateur) VALUES (:id)');
                $requete->execute(['id'=>$nouvelId]);
            }
            $db->commit();
            $identifiantsGeneres = ['nomUtilisateur' => $identifiant, 'motDePasse' => $motDePasse, 'matricule' => $matricule ?? null];
            $_SESSION['identifiants_personnel'] = $identifiantsGeneres;
            journaliserAction('Ajout d’un membre du personnel', $prenom . ' ' . $nom . ' (' . $roles[$role] . ')');
            flashMessage('success', 'Utilisateur ajouté avec succès.');
        }
        header('Location: ' . BASE_URL . 'views/directeur/personnel.php'); exit;
    } catch (Throwable $exception) { if ($db->inTransaction()) $db->rollBack(); $erreur = 'Impossible d’enregistrer ce membre : e-mail ou identifiant déjà utilisé.'; }
}
$pageTitle = $edition ? 'Modifier le personnel' : 'Ajouter un membre'; $activeMenu = 'personnel'; $contentClass = 'director-content'; require __DIR__ . '/../../includes/header.php';
?>
<div class="card director-panel personnel-form">
    <div class="director-heading">
        <div>
            <h2><?= htmlspecialchars($pageTitle) ?></h2>
            <p>Créez un accès professionnel avec un rôle clairement défini.</p>
        </div>
    </div>
    <?php if ($erreur): ?><p class="director-form-error" role="alert"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
    <?php if ($identifiantsGeneres): ?>
        <div class="card" style="border-left:4px solid #1e3a8a; margin-bottom:16px;">
            <strong>Identifiants de connexion générés</strong> — à communiquer au membre du personnel :<br>
            Identifiant : <code><?= htmlspecialchars($identifiantsGeneres['nomUtilisateur']) ?></code> —
            Mot de passe temporaire : <code><?= htmlspecialchars($identifiantsGeneres['motDePasse']) ?></code>
            <br><span style="font-size:12px; color:var(--muted);">La connexion se fait avec l'e-mail du membre du personnel, pas cet identifiant. Le mot de passe ci-dessus lui sera nécessaire pour se connecter.</span>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="director-form-grid">
            <label>Nom<input name="nom" required value="<?= htmlspecialchars($personnel['nom']) ?>"></label>
            <label>Prénom<input name="prenom" required value="<?= htmlspecialchars($personnel['prenom']) ?>"></label>
            <label>E-mail<input type="email" name="email" required value="<?= htmlspecialchars($personnel['email']) ?>"></label>
            <label>Téléphone<input name="telephone" value="<?= htmlspecialchars($personnel['telephone'] ?? '') ?>"></label>
            <?php if (!$edition): ?>
                <label>Rôle<select name="role" required><?php foreach ($roles as $cle => $libelle): ?><option value="<?= $cle ?>" <?= $cle === 'formateur' ? 'selected' : '' ?>><?= htmlspecialchars($libelle) ?></option><?php endforeach; ?></select></label>
            <?php endif; ?>
        </div>
        <div class="form-actions">
            <button class="btn" type="submit">Enregistrer le membre</button>
            <a class="btn" href="<?= htmlspecialchars(BASE_URL . 'views/directeur/personnel.php') ?>">Annuler</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>