<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['formateur', 'responsable']);
$db = database();
$contentClass = 'management-content';

$messageErreur = null;
$sessionSelectionnee = (int) ($_GET['seance'] ?? $_POST['idSeance'] ?? 0);
$sqlSeances = 'SELECT s.idSeance, s.idModule, s.dateSeance, s.heureDebut, s.heureFin, s.statut, m.libelle AS module FROM seance s INNER JOIN module m ON m.idModule = s.idModule';
if ($currentUser['role'] === 'formateur') {
    $sqlSeances .= ' WHERE s.idFormateur = :formateur';
    $requete = $db->prepare($sqlSeances . ' ORDER BY s.dateSeance DESC, s.heureDebut DESC');
    $requete->execute(['formateur' => $currentUser['id']]);
} else {
    $requete = $db->query($sqlSeances . ' ORDER BY s.dateSeance DESC, s.heureDebut DESC');
}
$seances = $requete->fetchAll();
$seance = null;
foreach ($seances as $candidate) if ((int) $candidate['idSeance'] === $sessionSelectionnee) $seance = $candidate;
if (!$seance && $seances) { $seance = $seances[0]; $sessionSelectionnee = (int) $seance['idSeance']; }

function appelEstOuvert(array $seance): bool
{
    if (!in_array($seance['statut'], ['PLANIFIEE', 'EN_COURS'], true) || $seance['dateSeance'] !== date('Y-m-d')) return false;
    $debut = strtotime($seance['dateSeance'] . ' ' . $seance['heureDebut']);
    return time() >= $debut && time() <= $debut + 1800;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $seance) {
    if (!appelEstOuvert($seance)) {
        $messageErreur = 'La feuille d’appel est disponible uniquement pour une séance en cours, pendant les 30 premières minutes.';
    } else {
        $requete = $db->prepare('SELECT DISTINCT e.idUtilisateur FROM etudiant e INNER JOIN dossier_etudiant d ON d.idEtudiant=e.idUtilisateur INNER JOIN inscription i ON i.idDossier=d.idDossier INNER JOIN niveau n ON n.idNiveau=i.idNiveau INNER JOIN semestre se ON se.idNiveau=n.idNiveau WHERE se.idSemestre=(SELECT idSemestre FROM module WHERE idModule=:module)');
        $requete->execute(['module' => $seance['idModule']]);
        $etudiantsAutorises = array_map('intval', $requete->fetchAll(PDO::FETCH_COLUMN));
        $enregistrer = $db->prepare('INSERT INTO presence (idSeance,idEtudiant,statutPresence,heureArrivee,validee) VALUES (:seance,:etudiant,:statut,CURRENT_TIME(),1) ON DUPLICATE KEY UPDATE statutPresence=VALUES(statutPresence),heureArrivee=VALUES(heureArrivee),validee=1');
        foreach ($etudiantsAutorises as $etudiantId) {
            $statut = $_POST['statut'][$etudiantId] ?? 'ABSENT';
            if (in_array($statut, ['PRESENT', 'ABSENT', 'RETARD', 'JUSTIFIE'], true)) $enregistrer->execute(['seance'=>$sessionSelectionnee, 'etudiant'=>$etudiantId, 'statut'=>$statut]);
        }
        journaliserAction('Validation de l’appel', 'Séance #' . $sessionSelectionnee);
        flashMessage('success', 'La feuille d’appel a été enregistrée.');
        header('Location: ' . BASE_URL . 'views/formateur/appel.php?seance=' . $sessionSelectionnee); exit;
    }
}

$etudiants = [];
if ($seance) {
    $requete = $db->prepare('SELECT e.idUtilisateur,u.prenom,u.nom,COALESCE(p.statutPresence,"ABSENT") AS statutPresence FROM etudiant e INNER JOIN utilisateur u ON u.idUtilisateur=e.idUtilisateur INNER JOIN dossier_etudiant d ON d.idEtudiant=e.idUtilisateur INNER JOIN inscription i ON i.idDossier=d.idDossier INNER JOIN niveau n ON n.idNiveau=i.idNiveau INNER JOIN semestre se ON se.idNiveau=n.idNiveau LEFT JOIN presence p ON p.idEtudiant=e.idUtilisateur AND p.idSeance=:seance WHERE se.idSemestre=(SELECT idSemestre FROM module WHERE idModule=:module) GROUP BY e.idUtilisateur,u.prenom,u.nom,p.statutPresence ORDER BY u.nom,u.prenom');
    $requete->execute(['seance'=>$sessionSelectionnee, 'module'=>$seance['idModule']]); $etudiants = $requete->fetchAll();
}
$appeluOuvert = $seance ? appelEstOuvert($seance) : false;
$pageTitle='Feuille d’appel'; $activeMenu='appel'; require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Feuille d’appel</h2><p>Le formateur ou le responsable pédagogique peut enregistrer les présences pendant les 30 premières minutes d’une séance en cours.</p></div></div>
<?php if ($messageErreur): ?><div class="form-error"><?= htmlspecialchars($messageErreur) ?></div><?php endif; ?>
<div class="card form-card attendance-card">
<form method="get" class="session-selector"><label>Séance<select name="seance" onchange="this.form.submit()"><?php foreach ($seances as $item): ?><option value="<?= (int)$item['idSeance'] ?>" <?= (int)$item['idSeance']===$sessionSelectionnee?'selected':'' ?>><?= htmlspecialchars($item['dateSeance'].' - '.substr($item['heureDebut'],0,5).' / '.$item['module']) ?></option><?php endforeach; ?></select></label></form>
<?php if ($seance): ?><div class="attendance-banner <?= $appeluOuvert?'attendance-open':'attendance-closed' ?>"><strong><?= $appeluOuvert?'Appel ouvert':'Appel fermé' ?></strong><span><?= $appeluOuvert?'Vous pouvez encore enregistrer les présences.':'La séance doit être en cours et l’appel doit être effectué dans les 30 minutes suivant son début.' ?></span></div>
<form method="post"><input type="hidden" name="idSeance" value="<?= $sessionSelectionnee ?>"><div class="attendance-list"><div class="attendance-head"><span>Étudiant</span><span>Présence</span></div><?php foreach ($etudiants as $etudiant): ?><div class="attendance-row"><strong><?= htmlspecialchars($etudiant['prenom'].' '.$etudiant['nom']) ?></strong><div class="attendance-options"><label><input type="radio" name="statut[<?= (int)$etudiant['idUtilisateur'] ?>]" value="PRESENT" <?= $etudiant['statutPresence']==='PRESENT'?'checked':'' ?> <?= !$appeluOuvert?'disabled':'' ?>> Présent</label><label><input type="radio" name="statut[<?= (int)$etudiant['idUtilisateur'] ?>]" value="ABSENT" <?= $etudiant['statutPresence']==='ABSENT'?'checked':'' ?> <?= !$appeluOuvert?'disabled':'' ?>> Absent</label><label><input type="radio" name="statut[<?= (int)$etudiant['idUtilisateur'] ?>]" value="RETARD" <?= $etudiant['statutPresence']==='RETARD'?'checked':'' ?> <?= !$appeluOuvert?'disabled':'' ?>> Retard</label></div></div><?php endforeach; ?></div><?php if ($appeluOuvert): ?><button class="btn btn-primary" type="submit">Enregistrer l’appel</button><?php endif; ?></form><?php else: ?><p class="empty-state">Aucune séance ne vous est affectée.</p><?php endif; ?>
</div><?php require __DIR__ . '/../../includes/footer.php'; ?>