<?php
// Ancienne URL conservée pour compatibilité : redirige vers la nouvelle page.
require_once __DIR__ . '/../../config/app.php';
header('Location: ' . BASE_URL . 'views/responsable/inscriptions.php', true, 301);
exit;
