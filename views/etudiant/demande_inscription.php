<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

$currentUser = requireRole(['etudiant']);

$controller = new InscriptionController();
$message = "";
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNiveau = $_POST['idNiveau'];
    $anneeScolaire = $_POST['anneeScolaire'];
    $idEtudiant = $currentUser['id'];

    if ($controller->demanderInscription($idEtudiant, $idNiveau, $anneeScolaire)) {
        $message = "Votre demande d'inscription a été soumise avec succès.";
    } else {
        $erreur = "Une erreur est survenue lors de la demande d'inscription.";
    }
}

$niveaux = database()->query('SELECT n.idNiveau, n.libelle, f.libelle AS formation FROM niveau n INNER JOIN formation f ON f.idFormation = n.idFormation ORDER BY f.libelle, n.ordre')->fetchAll();
?>

<?php $pageTitle = 'Demande d’inscription'; $showSidebar = true; require __DIR__ . '/../../includes/header.php'; ?>
    <h2 style="margin-top:0;">Demande d'inscription</h2>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($erreur): ?>
        <div class="erreur"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="anneeScolaire">Année scolaire :</label>
            <input type="text" id="anneeScolaire" name="anneeScolaire" value="2026-2027" required>
        </div>

        <div class="form-group">
            <label for="idNiveau">Niveau souhaité :</label>
            <select id="idNiveau" name="idNiveau" required>
                <option value="">Sélectionner un niveau</option>
                <?php foreach ($niveaux as $niveau): ?>
                    <option value="<?= (int) $niveau['idNiveau'] ?>"><?= htmlspecialchars($niveau['formation'] . ' - ' . $niveau['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Soumettre la demande</button>
    </form>

    <p><a href="etat_inscription.php">Voir l'état de ma demande</a></p>
<?php require __DIR__ . '/../../includes/footer.php'; ?>