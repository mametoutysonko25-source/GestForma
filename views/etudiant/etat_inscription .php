<?php
// À placer dans : views/etudiant/etat_inscription.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

// À VÉRIFIER : la clé exacte de $_SESSION['user']['role'] et ['id'] selon config/session.php
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'etudiant') {
    header('Location: ' . BASE_URL . 'views/auth/login.php');
    exit;
}

$controller = new InscriptionController();
$inscription = $controller->getEtatInscription($_SESSION['user']['id']);

$pageTitle = "État de l'inscription";
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';

$statutColor = '#e65100'; // en attente par défaut
$statutLabel = 'en-attente';
if (isset($inscription['statut'])) {
    if ($inscription['statut'] === 'VALIDEE') {
        $statutColor = '#2e7d32';
    } elseif ($inscription['statut'] !== 'EN_ATTENTE') {
        $statutColor = '#c62828';
    }
}
?>

<h1 style="margin-bottom:16px;">État de votre inscription</h1>

<?php if ($inscription): ?>
    <div class="card" style="max-width:480px;">
        <p><strong>Niveau :</strong> <?php echo htmlspecialchars($inscription['idNiveau']); ?></p>
        <p><strong>Date de demande :</strong> <?php echo htmlspecialchars($inscription['dateInscription']); ?></p>
        <p><strong>Statut :</strong>
            <span style="font-weight:bold; color:<?php echo $statutColor; ?>;">
                <?php echo htmlspecialchars($inscription['statut']); ?>
            </span>
        </p>
    </div>
<?php else: ?>
    <div class="card" style="max-width:480px; color:var(--muted);">
        <p>Aucune demande d'inscription trouvée.</p>
    </div>
<?php endif; ?>

<p style="margin-top:20px;"><a href="demande_inscription.php" class="btn">Nouvelle demande</a></p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
