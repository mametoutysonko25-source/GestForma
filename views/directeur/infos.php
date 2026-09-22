<?php
require_once __DIR__ . '/../../controllers/helpers.php';
requireRole(['directeur']);
$pageTitle = 'Infos du centre';
$activeMenu = 'infos';
$contentClass = 'director-content';
require __DIR__ . '/../../includes/header.php';
?>
<div class="director-heading"><div><h2>Informations globales du centre</h2><p>Coordonnées et informations générales de GestForm.</p></div></div>
<div class="director-grid"><div class="card director-panel"><h3>Centre de formation</h3><div class="director-form-grid"><div><p class="director-info-line"><small>Nom</small><strong>Centre de formation</strong></p><p class="director-info-line"><small>Adresse</small><strong>Dakar, Sénégal</strong></p></div><div><p class="director-info-line"><small>E-mail</small><strong>contact@gestform.example</strong></p><p class="director-info-line"><small>Téléphone</small><strong>+221 77 000 00 00</strong></p></div></div></div><div class="card director-panel"><h3>Rôle du Directeur</h3><p class="director-info-line">Consultation des indicateurs globaux, suivi de l'activité pédagogique et consultation du personnel.</p></div></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>