<?php
// À placer dans : views/etudiant/demande_inscription.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

// À VÉRIFIER : la clé exacte de $_SESSION['user']['role'] et ['id'] selon config/session.php
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'etudiant') {
<<<<<<< HEAD
    header('Location: ' . BASE_URL . 'views/auth/login.php');
=======
    header('Location: /views/auth/login.php');
>>>>>>> 5afcf4e06df978dbe79398a368641aed31957b21
    exit;
}

$controller = new InscriptionController();
$message = "";
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNiveau = $_POST['idNiveau'];
    $anneeScolaire = $_POST['anneeScolaire'];
    $idEtudiant = $_SESSION['user']['id'];

    if ($controller->demanderInscription($idEtudiant, $idNiveau, $anneeScolaire)) {
        $message = "Votre demande d'inscription a été soumise avec succès.";
    } else {
        $erreur = "Une erreur est survenue lors de la demande d'inscription.";
    }
}

$pageTitle = "Demande d'inscription";
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h1 style="margin-bottom:16px;">Demande d'inscription</h1>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<div class="card" style="max-width:480px;">
    <form method="POST">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="anneeScolaire">Année scolaire :</label>
            <input type="text" id="anneeScolaire" name="anneeScolaire" value="2026-2027" required style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="idNiveau">Niveau souhaité :</label>
            <select id="idNiveau" name="idNiveau" required style="width:100%; padding:8px;">
                <option value="">Sélectionner un niveau</option>
                <option value="1">L1 - Première année</option>
                <option value="2">L2 - Deuxième année</option>
                <option value="3">L3 - Troisième année</option>
            </select>
        </div>

        <button type="submit" class="btn" style="width:100%;">Soumettre la demande</button>
    </form>
</div>

<p style="margin-top:20px;"><a href="etat_inscription.php" class="btn">Voir l'état de ma demande</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
