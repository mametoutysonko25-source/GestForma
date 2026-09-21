<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';

requireRole(['responsable']);

function genererIdentifiant(PDO $db, string $prenom, string $nom): string
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

function genererMotDePasseTemporaire(): string
{
    return 'Etu' . random_int(1000, 9999) . '!';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$modeEdition = $id !== null;
$message = "";
$erreur = "";
$identifiantsGeneres = null;

$etudiant = [
    'nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '',
    'matricule' => '', 'dateNaissance' => '', 'lieuNaissance' => '', 'sexe' => '',
    'adresse' => '', 'situationProfessionnelle' => '', 'statutParcours' => 'En cours',
    'personneUrgence' => '', 'telephoneUrgence' => '',
];

if ($modeEdition) {
    $statement = database()->prepare(
        "SELECT u.idUtilisateur, u.nom, u.prenom, u.email, u.telephone,
                e.matricule, e.dateNaissance, e.lieuNaissance, e.sexe, e.adresse,
                e.situationProfessionnelle, e.statutParcours, e.personneUrgence, e.telephoneUrgence
         FROM etudiant e
         INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
         WHERE e.idUtilisateur = :id"
    );
    $statement->execute(['id' => $id]);
    $trouve = $statement->fetch();
    if (!$trouve) {
        header('Location: ' . BASE_URL . 'views/responsable/etudiants.php');
        exit;
    }
    $etudiant = $trouve;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etudiant = array_merge($etudiant, [
        'nom'                       => trim($_POST['nom'] ?? ''),
        'prenom'                    => trim($_POST['prenom'] ?? ''),
        'email'                     => trim($_POST['email'] ?? ''),
        'telephone'                 => trim($_POST['telephone'] ?? ''),
        'matricule'                 => trim($_POST['matricule'] ?? ''),
        'dateNaissance'             => trim($_POST['dateNaissance'] ?? ''),
        'lieuNaissance'             => trim($_POST['lieuNaissance'] ?? ''),
        'sexe'                      => trim($_POST['sexe'] ?? ''),
        'adresse'                   => trim($_POST['adresse'] ?? ''),
        'situationProfessionnelle'  => trim($_POST['situationProfessionnelle'] ?? ''),
        'statutParcours'            => trim($_POST['statutParcours'] ?? ''),
        'personneUrgence'           => trim($_POST['personneUrgence'] ?? ''),
        'telephoneUrgence'          => trim($_POST['telephoneUrgence'] ?? ''),
    ]);

    if ($etudiant['nom'] === '' || $etudiant['prenom'] === '' || $etudiant['email'] === '' || $etudiant['matricule'] === '') {
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
                    'nom' => $etudiant['nom'], 'prenom' => $etudiant['prenom'],
                    'email' => $etudiant['email'], 'telephone' => $etudiant['telephone'] ?: null,
                    'id' => $id,
                ]);

                $statement = $db->prepare(
                    "UPDATE etudiant SET matricule = :matricule, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance,
                            sexe = :sexe, adresse = :adresse, situationProfessionnelle = :situationProfessionnelle,
                            statutParcours = :statutParcours, personneUrgence = :personneUrgence, telephoneUrgence = :telephoneUrgence
                     WHERE idUtilisateur = :id"
                );
                $statement->execute([
                    'matricule' => $etudiant['matricule'],
                    'dateNaissance' => $etudiant['dateNaissance'] ?: null,
                    'lieuNaissance' => $etudiant['lieuNaissance'] ?: null,
                    'sexe' => $etudiant['sexe'] ?: null,
                    'adresse' => $etudiant['adresse'] ?: null,
                    'situationProfessionnelle' => $etudiant['situationProfessionnelle'] ?: null,
                    'statutParcours' => $etudiant['statutParcours'] ?: null,
                    'personneUrgence' => $etudiant['personneUrgence'] ?: null,
                    'telephoneUrgence' => $etudiant['telephoneUrgence'] ?: null,
                    'id' => $id,
                ]);

                $db->commit();
                $message = "Fiche de l'étudiant mise à jour avec succès.";
            } else {
                $nomUtilisateur = genererIdentifiant($db, $etudiant['prenom'], $etudiant['nom']);
                $motDePasse = genererMotDePasseTemporaire();

                $statement = $db->prepare(
                    "INSERT INTO utilisateur (nom, prenom, email, telephone, nomUtilisateur, motDePasseHash, statutCompte)
                     VALUES (:nom, :prenom, :email, :telephone, :nomUtilisateur, :motDePasseHash, 'ACTIF')"
                );
                $statement->execute([
                    'nom' => $etudiant['nom'], 'prenom' => $etudiant['prenom'],
                    'email' => $etudiant['email'], 'telephone' => $etudiant['telephone'] ?: null,
                    'nomUtilisateur' => $nomUtilisateur,
                    'motDePasseHash' => password_hash($motDePasse, PASSWORD_DEFAULT),
                ]);
                $nouvelId = (int) $db->lastInsertId();

                $statement = $db->prepare(
                    "INSERT INTO etudiant (idUtilisateur, matricule, dateNaissance, lieuNaissance, sexe, adresse,
                            situationProfessionnelle, statutParcours, personneUrgence, telephoneUrgence, dateInscription)
                     VALUES (:id, :matricule, :dateNaissance, :lieuNaissance, :sexe, :adresse,
                            :situationProfessionnelle, :statutParcours, :personneUrgence, :telephoneUrgence, CURDATE())"
                );
                $statement->execute([
                    'id' => $nouvelId,
                    'matricule' => $etudiant['matricule'],
                    'dateNaissance' => $etudiant['dateNaissance'] ?: null,
                    'lieuNaissance' => $etudiant['lieuNaissance'] ?: null,
                    'sexe' => $etudiant['sexe'] ?: null,
                    'adresse' => $etudiant['adresse'] ?: null,
                    'situationProfessionnelle' => $etudiant['situationProfessionnelle'] ?: null,
                    'statutParcours' => $etudiant['statutParcours'] ?: 'En cours',
                    'personneUrgence' => $etudiant['personneUrgence'] ?: null,
                    'telephoneUrgence' => $etudiant['telephoneUrgence'] ?: null,
                ]);

                $db->commit();
                $id = $nouvelId;
                $modeEdition = true;
                $identifiantsGeneres = ['nomUtilisateur' => $nomUtilisateur, 'motDePasse' => $motDePasse];
                $message = "Étudiant créé avec succès.";
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $erreur = ((int) $e->getCode() === 23000 || str_contains($e->getMessage(), 'Duplicate'))
                ? "Cet e-mail ou ce matricule est déjà utilisé par un autre compte."
                : "Une erreur est survenue lors de l'enregistrement.";
        }
    }
}

$pageTitle  = $modeEdition ? "Fiche détaillée d'un étudiant" : "Ajouter un étudiant";
$activeMenu = 'etudiants';
require __DIR__ . '/../../includes/header.php';
?>

<p style="margin-top:0;"><a href="<?= htmlspecialchars(BASE_URL . 'views/responsable/etudiants.php') ?>" style="color:var(--primary);">&larr; Retour à la liste</a></p>
<h2 style="margin-top:0;"><?= $modeEdition ? "Fiche détaillée d'un étudiant" : "Ajouter un étudiant" ?></h2>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
<?php if ($identifiantsGeneres): ?>
    <div class="card" style="border-left:4px solid #1e3a8a; margin-bottom:16px;">
        <strong>Identifiants de connexion générés</strong> — à communiquer à l'étudiant :<br>
        Identifiant : <code><?= htmlspecialchars($identifiantsGeneres['nomUtilisateur']) ?></code> —
        Mot de passe temporaire : <code><?= htmlspecialchars($identifiantsGeneres['motDePasse']) ?></code>
        <br><span style="font-size:12px; color:var(--muted);">La connexion se fait avec l'e-mail de l'étudiant, pas cet identifiant. Le mot de passe ci-dessus lui sera nécessaire pour se connecter.</span>
    </div>
<?php endif; ?>

<div class="card" style="max-width:720px;">
    <form method="POST">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 20px;">
            <div class="form-field">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" required>
            </div>
            <div class="form-field">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($etudiant['email']) ?>" required>
            </div>
            <div class="form-field">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($etudiant['telephone']) ?>">
            </div>
            <div class="form-field">
                <label for="matricule">Matricule</label>
                <input type="text" id="matricule" name="matricule" value="<?= htmlspecialchars($etudiant['matricule']) ?>" required>
            </div>
            <div class="form-field">
                <label for="dateNaissance">Date de naissance</label>
                <input type="date" id="dateNaissance" name="dateNaissance" value="<?= htmlspecialchars($etudiant['dateNaissance']) ?>">
            </div>
            <div class="form-field">
                <label for="lieuNaissance">Lieu de naissance</label>
                <input type="text" id="lieuNaissance" name="lieuNaissance" value="<?= htmlspecialchars($etudiant['lieuNaissance']) ?>">
            </div>
            <div class="form-field">
                <label for="sexe">Sexe</label>
                <select id="sexe" name="sexe" style="width:100%; padding:11px 12px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="">Non précisé</option>
                    <option value="Masculin" <?= $etudiant['sexe'] === 'Masculin' ? 'selected' : '' ?>>Masculin</option>
                    <option value="Féminin" <?= $etudiant['sexe'] === 'Féminin' ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>
            <div class="form-field" style="grid-column:1 / -1;">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($etudiant['adresse']) ?>">
            </div>
            <div class="form-field">
                <label for="situationProfessionnelle">Situation professionnelle</label>
                <input type="text" id="situationProfessionnelle" name="situationProfessionnelle" value="<?= htmlspecialchars($etudiant['situationProfessionnelle']) ?>">
            </div>
            <div class="form-field">
                <label for="statutParcours">Statut du parcours</label>
                <select id="statutParcours" name="statutParcours" style="width:100%; padding:11px 12px; border:1px solid #d1d5db; border-radius:6px;">
                    <?php foreach (['En cours', 'Diplômé', 'Suspendu', 'Abandon'] as $statut): ?>
                        <option value="<?= $statut ?>" <?= $etudiant['statutParcours'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="personneUrgence">Personne à contacter en urgence</label>
                <input type="text" id="personneUrgence" name="personneUrgence" value="<?= htmlspecialchars($etudiant['personneUrgence']) ?>">
            </div>
            <div class="form-field">
                <label for="telephoneUrgence">Téléphone d'urgence</label>
                <input type="text" id="telephoneUrgence" name="telephoneUrgence" value="<?= htmlspecialchars($etudiant['telephoneUrgence']) ?>">
            </div>
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:8px;"><?= $modeEdition ? 'Enregistrer les modifications' : 'Créer l\'étudiant' ?></button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
