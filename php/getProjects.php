<?php
// php/getProjects.php — return all projects as JSON for the frontend
require __DIR__ . '/db.php';

// Set JSON header
header('Content-Type: application/json');

try {
    // Prepare and execute query
    $stmt = $pdo->prepare('
        SELECT 
            id, 
            title, 
            description, 
            tech_tags, 
            github_url, 
            demo_url, 
            cover_image, 
            sort_order,
            featured
        FROM projects 
        WHERE featured = 1
        ORDER BY sort_order ASC, id ASC
    ');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map cover_image to image_url for frontend consistency
    $projects = array_map(function($project) {
        $project['image_url'] = $project['cover_image'];
        return $project;
    }, $rows);

    // Return success response
    echo json_encode([
        'ok' => true, 
        'data' => $projects,
        'count' => count($projects)
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // Log error for debugging (in production, log to file instead)
    error_log('Database error in getProjects.php: ' . $e->getMessage());
    
    // Return error response without exposing sensitive details
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'error' => 'Database query failed',
        'message' => 'Unable to retrieve projects. Please try again later.'
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Catch any other unexpected errors
    error_log('Unexpected error in getProjects.php: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'error' => 'Server error',
        'message' => 'An unexpected error occurred.'
    ], JSON_PRETTY_PRINT);
}
