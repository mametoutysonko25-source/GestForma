<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

$currentUser = requireRole(['etudiant']);
$controller = new InscriptionController();
$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNiveau = (int) ($_POST['idNiveau'] ?? 0);
    $anneeScolaire = trim((string) ($_POST['anneeScolaire'] ?? ''));
    if ($idNiveau > 0 && $anneeScolaire !== '') {
        if ($controller->demanderInscription($currentUser['id'], $idNiveau, $anneeScolaire)) {
            $message = 'Votre demande d’inscription a été soumise avec succès.';
        } else {
            $erreur = 'Une erreur est survenue lors de la demande d’inscription.';
        }
    } else {
        $erreur = 'Veuillez sélectionner un niveau et préciser l’année scolaire.';
    }
}

$niveauQuery = database()->query('SELECT idNiveau, libelle FROM niveau ORDER BY ordre ASC');
$niveaux = $niveauQuery->fetchAll();

$pageTitle = 'Demande d’inscription';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Demande d’inscription</h2>

<?php if ($message): ?>
    <div class="card" style="border-left:4px solid #2e7d32; margin-bottom:16px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if ($erreur): ?>
    <div class="card" style="border-left:4px solid #c62828; margin-bottom:16px;">
        <?= htmlspecialchars($erreur) ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width:560px;">
    <form method="POST">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="anneeScolaire">Année scolaire :</label>
            <input type="text" id="anneeScolaire" name="anneeScolaire" value="2026-2027" required style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;" for="idNiveau">Niveau souhaité :</label>
            <select id="idNiveau" name="idNiveau" required style="width:100%; padding:8px;">
                <option value="">Sélectionner un niveau</option>
                <?php foreach ($niveaux as $niveau): ?>
                    <option value="<?= (int) $niveau['idNiveau'] ?>"><?= htmlspecialchars($niveau['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn" style="width:100%;">Soumettre la demande</button>
    </form>
</div>

<p style="margin-top:20px;"><a href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/etat_inscription.php') ?>" class="btn">Voir l’état de ma demande</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
