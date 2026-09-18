<?php
require_once __DIR__ . '/../../controllers/helpers.php';

$pageTitle = $pageTitle ?? 'GestForm';
$pageDescription = $pageDescription ?? 'Cette page est disponible dans GestForm.';
$allowedRoles = $allowedRoles ?? [];

if ($allowedRoles) {
    $currentUser = requireRole($allowedRoles);
} else {
    $currentUser = currentUser();
}

$showSidebar = $showSidebar ?? true;
require __DIR__ . '/../../includes/header.php';
?>

<div class="card">
    <h2 style="margin-top:0;"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h2>
    <p style="color:var(--muted); margin-bottom:0;">
        <?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>