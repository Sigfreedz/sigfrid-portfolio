<?php
// php/getRadar.php — return skill radar data as JSON for the frontend
require __DIR__ . '/db.php';

try {
    // Fetch radar data ordered by sort_order
    $stmt = $pdo->prepare('SELECT id, label, value, sort_order FROM skill_radar ORDER BY sort_order ASC, id ASC');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure values are integers
    foreach ($rows as &$row) {
        $row['value'] = (int)$row['value'];
        $row['sort_order'] = (int)$row['sort_order'];
    }
    unset($row);

    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $rows]);
} catch (PDOException $e) {
    // Quietly fail with JSON error
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'database query failed']);
}
