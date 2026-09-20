<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../controllers/RemunerationController.php';

requireRole(['comptable']);
$controller = new RemunerationController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer'])) {
        $controller->delete((int) $_POST['supprimer']);
    } elseif (isset($_POST['enregistrer'])) {
        $controller->save($_POST);
    }
    header('Location: remunerations.php');
    exit;
}
$remunerations = $controller->all();
$formateurs = $controller->formateurs();

$pageTitle = 'Rémunérations';
$showSidebar = true;
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Rémunérations à payer</h2>
<div class="card" style="max-width:640px; margin-bottom:20px;"><h3>Ajouter une rémunération</h3><form method="post"><label>Formateur<select name="idFormateur" required><?php foreach ($formateurs as $formateur): ?><option value="<?= (int) $formateur['idUtilisateur'] ?>"><?= htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']) ?></option><?php endforeach; ?></select></label><label>Mois<input type="month" name="mois" required></label><label>Heures validées<input type="number" name="heuresValidees" min="0" step="0.01" required></label><label>Montant dû<input type="number" name="montantDu" min="0" step="0.01" required></label><label>Montant payé<input type="number" name="montantPaye" min="0" step="0.01" value="0" required></label><label>Statut<select name="statut"><option>NON_PAYE</option><option>PARTIEL</option><option>PAYE</option></select></label><button class="btn" name="enregistrer" type="submit">Enregistrer</button></form></div>
<div class="card" style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:var(--primary); color:#fff;">
                <th style="padding:10px; text-align:left;">Formateur</th>
                <th style="padding:10px; text-align:left;">Mois</th>
                <th style="padding:10px; text-align:left;">Heures</th>
                <th style="padding:10px; text-align:left;">Montant dû</th>
                <th style="padding:10px; text-align:left;">Montant payé</th>
                <th style="padding:10px; text-align:left;">Statut</th><th style="padding:10px; text-align:left;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($remunerations as $remuneration): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['prenom'] . ' ' . $remuneration['nom']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['mois']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($remuneration['heuresValidees']) ?></td>
                    <td style="padding:10px;"><?= number_format((float) $remuneration['montantDu'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px;"><?= number_format((float) $remuneration['montantPaye'], 2, ',', ' ') ?> FCFA</td>
                    <td style="padding:10px; color:#c62828; font-weight:bold;"><?= htmlspecialchars($remuneration['statut']) ?></td><td style="padding:10px;"><form method="post"><button name="supprimer" value="<?= (int) $remuneration['idRemuneration'] ?>" onclick="return confirm('Supprimer cette rémunération ?')">Supprimer</button></form></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$remunerations): ?>
                <tr><td colspan="7" style="padding:16px; text-align:center; color:var(--muted);">Aucune rémunération enregistrée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
