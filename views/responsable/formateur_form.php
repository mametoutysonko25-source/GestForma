<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

function genererIdentifiantFormateur(PDO $db, string $prenom, string $nom): string
{
    $base = strtolower(trim($prenom) . '.' . trim($nom));
    $base = preg_replace('/[^a-z0-9.]+/', '', strtr($base, [
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'â' => 'a',
        'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ù' => 'u', 'ç' => 'c',
    ]));
    $base = $base !== '' ? $base : 'utilisateur';
    $candidat = $base;
    $suffixe = 1;
    $statement = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE nomUtilisateur = :nomUtilisateur");
    while (true) {
        $statement->execute(['nomUtilisateur' => $candidat]);
        if ((int) $statement->fetchColumn() === 0) {
            return $candidat;
        }
        $suffixe++;
        $candidat = $base . $suffixe;
    }
}

function genererMotDePasseTemporaireFormateur(): string
{
    return 'For' . random_int(1000, 9999) . '!';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$modeEdition = $id !== null;
$message = "";
$erreur = "";
$identifiantsGeneres = null;

$formateur = [
    'nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '',
    'matricule' => '', 'specialite' => '', 'adresse' => '', 'experience' => '',
    'disponibilites' => '', 'tarifHoraire' => '',
];

if ($modeEdition) {
    $statement = database()->prepare(
        "SELECT u.idUtilisateur, u.nom, u.prenom, u.email, u.telephone,
                f.matricule, f.specialite, f.adresse, f.experience, f.disponibilites, f.tarifHoraire
         FROM formateur f
         INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
         WHERE f.idUtilisateur = :id"
    );
    $statement->execute(['id' => $id]);
    $trouve = $statement->fetch();
    if (!$trouve) {
        header('Location: ' . BASE_URL . 'views/responsable/formateurs.php');
        exit;
    }
    $formateur = $trouve;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formateur = array_merge($formateur, [
        'nom'             => trim($_POST['nom'] ?? ''),
        'prenom'          => trim($_POST['prenom'] ?? ''),
        'email'           => trim($_POST['email'] ?? ''),
        'telephone'       => trim($_POST['telephone'] ?? ''),
        'matricule'       => trim($_POST['matricule'] ?? ''),
        'specialite'      => trim($_POST['specialite'] ?? ''),
        'adresse'         => trim($_POST['adresse'] ?? ''),
        'experience'      => trim($_POST['experience'] ?? ''),
        'disponibilites'  => trim($_POST['disponibilites'] ?? ''),
        'tarifHoraire'    => trim($_POST['tarifHoraire'] ?? ''),
    ]);

    if ($formateur['nom'] === '' || $formateur['prenom'] === '' || $formateur['email'] === '' || $formateur['matricule'] === '') {
        $erreur = "Le nom, le prénom, l'email et le matricule sont obligatoires.";
    } else {
        $db = database();
        try {
            $db->beginTransaction();

            if ($modeEdition) {
                $statement = $db->prepare(
                    "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE idUtilisateur = :id"
                );
                $statement->execute([
                    'nom' => $formateur['nom'], 'prenom' => $formateur['prenom'],
                    'email' => $formateur['email'], 'telephone' => $formateur['telephone'] ?: null,
                    'id' => $id,
                ]);

                $statement = $db->prepare(
                    "UPDATE formateur SET matricule = :matricule, specialite = :specialite, adresse = :adresse,
                            experience = :experience, disponibilites = :disponibilites, tarifHoraire = :tarifHoraire
                     WHERE idUtilisateur = :id"
                );
                $statement->execute([
                    'matricule' => $formateur['matricule'],
                    'specialite' => $formateur['specialite'] ?: null,
                    'adresse' => $formateur['adresse'] ?: null,
                    'experience' => $formateur['experience'] !== '' ? (int) $formateur['experience'] : null,
                    'disponibilites' => $formateur['disponibilites'] ?: null,
                    'tarifHoraire' => $formateur['tarifHoraire'] !== '' ? (float) $formateur['tarifHoraire'] : 0,
                    'id' => $id,
                ]);

                $db->commit();
                $message = "Fiche du formateur mise à jour avec succès.";
            } else {
                $nomUtilisateur = genererIdentifiantFormateur($db, $formateur['prenom'], $formateur['nom']);
                $motDePasse = genererMotDePasseTemporaireFormateur();

                $statement = $db->prepare(
                    "INSERT INTO utilisateur (nom, prenom, email, telephone, nomUtilisateur, motDePasseHash, statutCompte)
                     VALUES (:nom, :prenom, :email, :telephone, :nomUtilisateur, :motDePasseHash, 'ACTIF')"
                );
                $statement->execute([
                    'nom' => $formateur['nom'], 'prenom' => $formateur['prenom'],
                    'email' => $formateur['email'], 'telephone' => $formateur['telephone'] ?: null,
                    'nomUtilisateur' => $nomUtilisateur,
                    'motDePasseHash' => password_hash($motDePasse, PASSWORD_DEFAULT),
                ]);
                $nouvelId = (int) $db->lastInsertId();

                $statement = $db->prepare(
                    "INSERT INTO formateur (idUtilisateur, matricule, specialite, adresse, experience, disponibilites, tarifHoraire)
                     VALUES (:id, :matricule, :specialite, :adresse, :experience, :disponibilites, :tarifHoraire)"
                );
                $statement->execute([
                    'id' => $nouvelId,
                    'matricule' => $formateur['matricule'],
                    'specialite' => $formateur['specialite'] ?: null,
                    'adresse' => $formateur['adresse'] ?: null,
                    'experience' => $formateur['experience'] !== '' ? (int) $formateur['experience'] : null,
                    'disponibilites' => $formateur['disponibilites'] ?: null,
                    'tarifHoraire' => $formateur['tarifHoraire'] !== '' ? (float) $formateur['tarifHoraire'] : 0,
                ]);

                $db->commit();
                $id = $nouvelId;
                $modeEdition = true;
                $identifiantsGeneres = ['nomUtilisateur' => $nomUtilisateur, 'motDePasse' => $motDePasse];
                $message = "Formateur créé avec succès.";
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $erreur = ((int) $e->getCode() === 23000 || str_contains($e->getMessage(), 'Duplicate'))
                ? "Cet e-mail ou ce matricule est déjà utilisé par un autre compte."
                : "Une erreur est survenue lors de l'enregistrement.";
        }
    }
}

$pageTitle  = $modeEdition ? "Fiche détaillée d'un formateur" : "Ajouter un formateur";
$activeMenu = 'formateurs';
require __DIR__ . '/../../includes/header.php';
?>

<p style="margin-top:0;"><a href="<?= htmlspecialchars(BASE_URL . 'views/responsable/formateurs.php') ?>" style="color:var(--primary);">&larr; Retour à la liste</a></p>
<h2 style="margin-top:0;"><?= $modeEdition ? "Fiche détaillée d'un formateur" : "Ajouter un formateur" ?></h2>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
<?php if ($identifiantsGeneres): ?>
    <div class="card" style="border-left:4px solid #1e3a8a; margin-bottom:16px;">
        <strong>Identifiants de connexion générés</strong> — à communiquer au formateur :<br>
        Identifiant : <code><?= htmlspecialchars($identifiantsGeneres['nomUtilisateur']) ?></code> —
        Mot de passe temporaire : <code><?= htmlspecialchars($identifiantsGeneres['motDePasse']) ?></code>
        <br><span style="font-size:12px; color:var(--muted);">La connexion se fait avec l'e-mail du formateur, pas cet identifiant. Le mot de passe ci-dessus lui sera nécessaire pour se connecter.</span>
    </div>
<?php endif; ?>

<div class="card" style="max-width:720px;">
    <form method="POST">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 20px;">
            <div class="form-field">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($formateur['nom']) ?>" required>
            </div>
            <div class="form-field">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($formateur['prenom']) ?>" required>
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($formateur['email']) ?>" required>
            </div>
            <div class="form-field">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($formateur['telephone']) ?>">
            </div>
            <div class="form-field">
                <label for="matricule">Matricule</label>
                <input type="text" id="matricule" name="matricule" value="<?= htmlspecialchars($formateur['matricule']) ?>" required>
            </div>
            <div class="form-field">
                <label for="specialite">Spécialité</label>
                <input type="text" id="specialite" name="specialite" value="<?= htmlspecialchars($formateur['specialite']) ?>">
            </div>
            <div class="form-field" style="grid-column:1 / -1;">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($formateur['adresse']) ?>">
            </div>
            <div class="form-field">
                <label for="experience">Expérience (années)</label>
                <input type="number" id="experience" name="experience" min="0" value="<?= htmlspecialchars((string) $formateur['experience']) ?>">
            </div>
            <div class="form-field">
                <label for="tarifHoraire">Tarif horaire (FCFA)</label>
                <input type="number" id="tarifHoraire" name="tarifHoraire" min="0" step="0.01" value="<?= htmlspecialchars((string) $formateur['tarifHoraire']) ?>">
            </div>
            <div class="form-field" style="grid-column:1 / -1;">
                <label for="disponibilites">Disponibilités</label>
                <input type="text" id="disponibilites" name="disponibilites" value="<?= htmlspecialchars($formateur['disponibilites']) ?>" placeholder="Ex : Lundi-Vendredi, 8h-13h">
            </div>
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:8px;"><?= $modeEdition ? 'Enregistrer les modifications' : 'Créer le formateur' ?></button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
