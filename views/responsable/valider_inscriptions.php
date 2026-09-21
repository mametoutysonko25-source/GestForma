<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

requireRole(['responsable']);
$controller = new InscriptionController();
$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idInscription = (int) ($_POST['idInscription'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($idInscription <= 0 || !in_array($action, ['valider', 'refuser'], true)) {
        $erreur = 'Action invalide.';
    } elseif ($action === 'valider' && $controller->validerInscription($idInscription)) {
        $message = 'Inscription validée avec succès.';
    } elseif ($action === 'refuser' && $controller->refuserInscription($idInscription)) {
        $message = 'Inscription refusée.';
    } else {
        $erreur = 'La mise à jour de l’inscription a échoué.';
    }
}

$inscriptions = $controller->getInscriptionsEnAttente();
$pageTitle = 'Validation des inscriptions';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Demandes d'inscription en attente</h2>
<?php if ($message): ?><p style="color:#217a4b;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($erreur): ?><p style="color:#c62828;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Étudiant</th>
                <th style="padding:10px; text-align:left;">Matricule</th>
                <th style="padding:10px; text-align:left;">Niveau</th>
                <th style="padding:10px; text-align:left;">Année scolaire</th>
                <th style="padding:10px; text-align:left;">Date</th>
                <th style="padding:10px; text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inscriptions as $inscription): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:10px;"><?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($inscription['matricule']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($inscription['niveau_libelle']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($inscription['anneeScolaire']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($inscription['dateInscription']) ?></td>
                <td style="padding:10px; white-space:nowrap;">
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="idInscription" value="<?= (int) $inscription['idInscription'] ?>">
                        <button class="btn" type="submit" name="action" value="valider">Valider</button>
                    </form>
                    <form method="post" style="display:inline; margin-left:6px;" onsubmit="return confirm('Refuser cette demande ?');">
                        <input type="hidden" name="idInscription" value="<?= (int) $inscription['idInscription'] ?>">
                        <button type="submit" name="action" value="refuser" style="border:0; background:none; color:#c62828; cursor:pointer;">Refuser</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$inscriptions): ?>
            <tr><td colspan="6" style="padding:16px; text-align:center; color:var(--muted);">Aucune demande en attente.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>