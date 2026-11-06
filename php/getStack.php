<?php
// php/getStack.php — return tech stack items as JSON for frontend (supports ?featured=1)
require __DIR__ . '/db.php';

try {
    $featuredOnly = isset($_GET['featured']) && $_GET['featured'] === '1';

    if ($featuredOnly) {
        $stmt = $pdo->prepare('SELECT id, name, category, icon, sort_order, featured FROM skills WHERE featured = 1 ORDER BY sort_order ASC, id ASC');
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare('SELECT id, name, category, icon, sort_order, featured FROM skills ORDER BY category ASC, sort_order ASC, id ASC');
        $stmt->execute();
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normalize icon paths / fallback
    foreach ($rows as &$row) {
        if (empty($row['icon'])) {
            $row['icon'] = 'placeholder.svg';
        }
    }
    unset($row);

    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $rows]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'database query failed']);
}
