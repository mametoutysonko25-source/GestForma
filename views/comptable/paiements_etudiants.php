<?php
session_start();
require_once __DIR__ . '/../../controllers/PaiementController.php';

if (!isset($_SESSION['idUtilisateur'])) {
    header("Location: ../../index.php");
    exit();
}

$controller = new PaiementController();
$inscriptions = $controller->getInscriptionsValidees();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des paiements</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .btn { padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border: none; cursor: pointer; }
        .solde-positif { color: green; font-weight: bold; }
        .solde-negatif { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Gestion des paiements étudiants</h1>

    <table>
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Matricule</th>
                <th>Email</th>
                <th>Niveau</th>
                <th>Année scolaire</th>
                <th>Date inscription</th>
                <th>Total payé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $ins): ?>
                <tr>
                    <td><?php echo $ins['nom'] . ' ' . $ins['prenom']; ?></td>
                    <td><?php echo $ins['matricule']; ?></td>
                    <td><?php echo $ins['email']; ?></td>
                    <td><?php echo $ins['niveau_libelle']; ?></td>
                    <td><?php echo $ins['anneeScolaire']; ?></td>
                    <td><?php echo $ins['dateInscription']; ?></td>
                    <td class="<?php echo $ins['total_paye'] > 0 ? 'solde-positif' : 'solde-negatif'; ?>">
                        <?php echo number_format($ins['total_paye'], 2, ',', ' '); ?> FCFA
                    </td>
                    <td>
                        <a href="enregistrer_paiement.php?idInscription=<?php echo $ins['idInscription']; ?>" class="btn">
                            Enregistrer paiement
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="enregistrer_paiement.php">Nouveau paiement</a></p>
</body>
</html>