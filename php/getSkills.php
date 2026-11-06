<?php
// php/getSkills.php — return all skills as JSON for the frontend
require __DIR__ . '/db.php';

try {
    $stmt = $pdo->prepare('SELECT id, name, category, icon, level, sort_order FROM skills ORDER BY sort_order ASC, id ASC');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normalize icon paths: if empty, assign a placeholder so UI never breaks
    foreach ($rows as &$row) {
        if (empty($row['icon'])) {
            $row['icon'] = 'icons/placeholder.svg';
        }
    }
    unset($row);

    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $rows]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'database query failed']);
}