<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/helpers.php';

$currentUser = requireRole(['administrateur']);
$pageTitle = 'Journal des actions';
$logs = [];
$columns = [];
$databaseError = null;

try {
    $connection = database();
    $columns = $connection->query('SHOW COLUMNS FROM journal_activites')->fetchAll();

    if ($columns) {
        $columnNames = array_column($columns, 'Field');
        $orderColumn = in_array('dateAction', $columnNames, true)
            ? 'dateAction'
            : ($columnNames[0] ?? null);
        $orderBy = $orderColumn ? ' ORDER BY `' . str_replace('`', '``', $orderColumn) . '` DESC' : '';
        $logs = $connection->query('SELECT * FROM journal_activites' . $orderBy . ' LIMIT 100')->fetchAll();
    }
} catch (PDOException $exception) {
    $databaseError = 'Le journal des actions n’est pas encore disponible dans la base de données.';
}

$formatValue = static function ($value): string {
    if ($value === null || $value === '') {
        return '—';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

require __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:20px;">
    <div>
        <h2 style="margin:0;">Journal des actions</h2>
        <p style="color:var(--muted); font-size:13px; margin:6px 0 0;">
            Les 100 dernières actions enregistrées dans l’application.
        </p>
    </div>
    <a class="btn" href="/admin/journal.php">Actualiser</a>
</div>

<?php if ($databaseError): ?>
    <div class="card">
        <p style="margin:0; color:var(--muted);"><?= htmlspecialchars($databaseError, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
<?php elseif (!$columns): ?>
    <div class="card">
        <p style="margin:0; color:var(--muted);">Aucune structure de journal n’est configurée.</p>
    </div>
<?php elseif (!$logs): ?>
    <div class="card">
        <p style="margin:0; color:var(--muted);">Aucune action enregistrée.</p>
    </div>
<?php else: ?>
    <div class="card" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th style="text-align:left; padding:10px 8px; border-bottom:1px solid #E5E7EB; white-space:nowrap;">
                            <?= htmlspecialchars($column['Field'], ENT_QUOTES, 'UTF-8') ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <td style="padding:10px 8px; border-bottom:1px solid #F0F2F5; vertical-align:top;">
                                <?= $formatValue($log[$column['Field']] ?? null) ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>