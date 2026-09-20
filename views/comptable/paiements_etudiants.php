<?php
require_once __DIR__ . '/../../controllers/helpers.php'; require_once __DIR__ . '/../../controllers/PaiementController.php';
requireRole(['comptable']); $controller = new PaiementController();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) { $controller->supprimerPaiement((int) $_POST['supprimer']); header('Location: paiements_etudiants.php'); exit; }
$paiements = $controller->getAllPaiements(); $pageTitle = 'Paiements étudiants'; $showSidebar = true; require __DIR__ . '/../../includes/header.php';
?>
<h1>Paiements étudiants</h1><p><a class="btn" href="enregistrer_paiement.php">Nouveau paiement</a></p>
<div class="card" style="overflow-x:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th>Date</th><th>Étudiant</th><th>Inscription</th><th>Montant</th><th>Mode</th><th>Référence</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($paiements as $paiement): ?><tr><td><?= htmlspecialchars($paiement['datePaiement']) ?></td><td><?= htmlspecialchars($paiement['prenom'] . ' ' . $paiement['nom']) ?></td><td><?= (int) $paiement['idInscription'] ?></td><td><?= number_format((float) $paiement['montant'], 2, ',', ' ') ?> FCFA</td><td><?= htmlspecialchars($paiement['modePaiement']) ?></td><td><?= htmlspecialchars((string) $paiement['referencePaiement']) ?></td><td><a href="enregistrer_paiement.php?idPaiement=<?= (int) $paiement['idPaiement'] ?>">Modifier</a> <form method="post" style="display:inline"><button name="supprimer" value="<?= (int) $paiement['idPaiement'] ?>" onclick="return confirm('Supprimer ce paiement ?')">Supprimer</button></form></td></tr><?php endforeach; ?>
<?php if (!$paiements): ?><tr><td colspan="7">Aucun paiement enregistré.</td></tr><?php endif; ?></tbody></table></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>