<?php
require __DIR__ . '/_bootstrap.php';
$requete = $db->prepare('SELECT DATE_FORMAT(s.dateSeance,"%Y-%m") AS mois, SUM(TIME_TO_SEC(TIMEDIFF(s.heureFin,s.heureDebut)))/3600 AS heures, f.tarifHoraire, (SUM(TIME_TO_SEC(TIMEDIFF(s.heureFin,s.heureDebut)))/3600) * f.tarifHoraire AS montant FROM seance s INNER JOIN formateur f ON f.idUtilisateur=s.idFormateur WHERE s.idFormateur=:id AND s.dateSeance <= CURRENT_DATE() GROUP BY DATE_FORMAT(s.dateSeance,"%Y-%m"),f.tarifHoraire ORDER BY mois DESC');
$requete->execute(['id'=>$formateurId]); $lignes=$requete->fetchAll();
$requete=$db->prepare('SELECT tarifHoraire FROM formateur WHERE idUtilisateur=:id'); $requete->execute(['id'=>$formateurId]); $tarif=(float)($requete->fetchColumn() ?: 0);
$totalHeures=0; $totalMontant=0; foreach($lignes as $ligne){$totalHeures+=(float)$ligne['heures'];$totalMontant+=(float)$ligne['montant'];}
$pageTitle='Rémunération'; $activeMenu='remuneration'; require __DIR__.'/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Ma rémunération</h2><p>Calcul automatique des heures de cours réalisées au tarif de <?= number_format($tarif,0,',',' ') ?> F / heure.</p></div></div>
<div class="remuneration-kpis"><div><span>Total heures réalisées</span><strong><?= number_format($totalHeures,2,',',' ') ?> h</strong></div><div><span>Montant cumulé</span><strong><?= number_format($totalMontant,0,',',' ') ?> F</strong></div><div><span>Tarif horaire</span><strong><?= number_format($tarif,0,',',' ') ?> F</strong></div></div>
<div class="card table-card"><table class="director-table"><thead><tr><th>Mois</th><th>Heures réalisées</th><th>Tarif horaire</th><th>Montant calculé</th><th>Base</th></tr></thead><tbody><?php foreach($lignes as $ligne): ?><tr><td><?= htmlspecialchars($ligne['mois']) ?></td><td><?= number_format((float)$ligne['heures'],2,',',' ') ?> h</td><td><?= number_format((float)$ligne['tarifHoraire'],0,',',' ') ?> F</td><td><strong><?= number_format((float)$ligne['montant'],0,',',' ') ?> F</strong></td><td>Séances réalisées</td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__.'/../../includes/footer.php'; ?>