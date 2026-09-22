<?php
require_once __DIR__ . '/../../controllers/helpers.php';
$currentUser = requireRole(['formateur']);
$db = database();
$formateurId = (int) $currentUser['id'];
$contentClass = 'management-content';