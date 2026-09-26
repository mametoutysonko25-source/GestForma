<?php
require_once __DIR__ . '/../../controllers/helpers.php';
require_once __DIR__ . '/../../config/database.php';
$currentUser = requireRole(['etudiant']);
$db = database();

$inscription = $db->prepare(
    "SELECT i.idInscription, i.statut AS statutInscription,
            COALESCE(SUM(p.montant), 0) AS totalPaye,
            COUNT(p.idPaiement) AS nombrePaiements
     FROM etudiant e
     LEFT JOIN dossier_etudiant d ON d.idEtudiant = e.idUtilisateur
     LEFT JOIN inscription i ON i.idDossier = d.idDossier
     LEFT JOIN paiement p ON p.idInscription = i.idInscription
     WHERE e.idUtilisateur = :id
     GROUP BY i.idInscription, i.statut
     ORDER BY i.idInscription DESC
     LIMIT 1"
);
$inscription->execute(['id' => $currentUser['id']]);
$inscription = $inscription->fetch();

$presence = $db->prepare(
    "SELECT
        COUNT(*) AS totalSeances,
        SUM(CASE WHEN p.statutPresence = 'PRESENT' THEN 1 ELSE 0 END) AS presentes
     FROM presence p
     WHERE p.idEtudiant = :id"
);
$presence->execute(['id' => $currentUser['id']]);
$presence = $presence->fetch();

$moyenne = $db->prepare(
    "SELECT COALESCE(AVG(r.note), 0) AS moyenne
     FROM resultat r
     WHERE r.idEtudiant = :id"
);
$moyenne->execute(['id' => $currentUser['id']]);
$moyenne = (float) $moyenne->fetchColumn();

$modules = $db->prepare(
    "SELECT COUNT(*)
     FROM inscription i
     INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
     INNER JOIN niveau n ON n.idNiveau = i.idNiveau
     INNER JOIN semestre s ON s.idNiveau = n.idNiveau
     INNER JOIN module m ON m.idSemestre = s.idSemestre
     WHERE d.idEtudiant = :id"
);
$modules->execute(['id' => $currentUser['id']]);
$nbModules = (int) $modules->fetchColumn();

$tauxPresence = ($presence['totalSeances'] > 0)
    ? round((($presence['presentes'] ?? 0) / $presence['totalSeances']) * 100)
    : 0;

$pageTitle  = "Tableau de bord";
$activeMenu = "dashboard";
require __DIR__ . '/../../includes/header.php';
?>

<h2 style="margin-top:0;">Tableau de bord étudiant</h2>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Modules</div>
        <div style="font-size:22px; font-weight:bold;"><?= $nbModules ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Présences</div>
        <div style="font-size:22px; font-weight:bold;"><?= $tauxPresence ?>%</div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Moyenne</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format($moyenne, 2, ',', ' ') ?></div>
    </div>
    <div class="card">
        <div style="font-size:12px; color:var(--muted);">Montant payé</div>
        <div style="font-size:22px; font-weight:bold;"><?= number_format((float) ($inscription['totalPaye'] ?? 0), 0, ',', ' ') ?> FCFA</div>
    </div>
</div>

<div class="card">
    <p style="color:var(--muted); font-size:13px;">
        Bienvenue <strong><?= htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></strong>.
        Votre inscription actuelle est : <strong><?= htmlspecialchars($inscription['statutInscription'] ?? 'Aucune inscription') ?></strong>.
    </p>
    <p style="margin-top:12px;">
        <a href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/dossier.php') ?>" class="btn">Mon dossier</a>
        <a href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/finance.php') ?>" class="btn">Situation financière</a>
        <a href="<?= htmlspecialchars(BASE_URL . 'views/etudiant/etat_inscription.php') ?>" class="btn">État inscription</a>
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
