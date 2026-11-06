<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode([
        'ok' => false,
        'status' => 'ERROR',
        'error' => 'Only POST requests are supported.',
    ]);
    exit;
}

$manifest = require __DIR__ . '/playground-manifest.php';
require __DIR__ . '/playground/DavidStar.php';
require __DIR__ . '/playground/NumberedTriangle.php';
require __DIR__ . '/playground/LootMaster.php';
require __DIR__ . '/playground/GigaPet.php';
require __DIR__ . '/playground/LuckyNine.php';

$request = $_POST;
if ($request === []) {
    $rawBody = file_get_contents('php://input');
    $decodedBody = json_decode($rawBody ?: '', true);
    if (is_array($decodedBody)) {
        $request = $decodedBody;
    }
}

$projectId = is_string($request['project'] ?? null) ? $request['project'] : '';
if (!isset($manifest[$projectId])) {
    http_response_code(404);
    echo json_encode([
        'ok' => false,
        'project' => $projectId,
        'status' => 'ERROR',
        'error' => 'That Playground project is not available.',
    ]);
    exit;
}

$project = $manifest[$projectId];
$handler = $project['handler'];

if ($projectId === 'david-star') {
    $size = filter_var($request['size'] ?? null, FILTER_VALIDATE_INT);
    if ($size === false || $size === null) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => 'Size must be a whole number.',
        ]);
        exit;
    }
    if ($size < $project['input']['min'] || $size > $project['input']['max'] || $size % 2 === 0) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => 'Size must be an odd number from 7 to 15.',
        ]);
        exit;
    }
    $result = $handler($size);
} elseif ($projectId === 'numbered-triangle') {
    $height = filter_var($request['height'] ?? null, FILTER_VALIDATE_INT);
    if ($height === false || $height === null || $height < 1 || $height > 30) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => 'Height must be a whole number from 1 to 30.',
        ]);
        exit;
    }
    $result = $handler($height);
} elseif ($projectId === 'loot-master') {
    try {
        $result = $handler($request);
    } catch (InvalidArgumentException $exception) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => $exception->getMessage(),
        ]);
        exit;
    }
} elseif ($projectId === 'gigapet') {
    $name = trim((string) ($request['name'] ?? ''));
    $type = trim((string) ($request['type'] ?? ''));
    $action = is_string($request['action'] ?? null) ? $request['action'] : '';
    if ($name === '' || strlen($name) > 30 || $type === '' || strlen($type) > 30) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => 'Pet name and type must each contain 1 to 30 characters.',
        ]);
        exit;
    }
    if (!in_array($action, ['feed', 'play', 'status'], true)) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => 'Choose feed, play, or status.',
        ]);
        exit;
    }
    if ($action !== 'status') {
        $amount = filter_var($request['amount'] ?? null, FILTER_VALIDATE_INT);
        if ($amount === false || $amount === null || $amount < 1 || $amount > 10) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'project' => $projectId,
                'status' => 'ERROR',
                'error' => 'Interaction count must be a whole number from 1 to 10.',
            ]);
            exit;
        }
    }
    try {
        $result = $handler($request);
    } catch (InvalidArgumentException $exception) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => $exception->getMessage(),
        ]);
        exit;
    }
} elseif ($projectId === 'lucky-nine') {
    try {
        $action = is_string($request['action'] ?? null) ? $request['action'] : '';
        if ($action === 'deal') {
            $players = filter_var($request['players'] ?? null, FILTER_VALIDATE_INT);
            if ($players === false || $players === null || $players < 1 || $players > 20) {
                throw new InvalidArgumentException('Players must be a whole number from 1 to 20.');
            }
        }
        $result = $handler($request);
    } catch (InvalidArgumentException $exception) {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'project' => $projectId,
            'status' => 'ERROR',
            'error' => $exception->getMessage(),
        ]);
        exit;
    }
} else {
    http_response_code(409);
    echo json_encode([
        'ok' => false,
        'project' => $projectId,
        'status' => 'ERROR',
        'error' => 'That project is not runnable yet.',
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'project' => $projectId,
    'status' => $result['status'] ?? 'COMPLETE',
    'output' => $result['output'] ?? '',
    'label' => $result['label'],
    'steps' => $result['steps'] ?? [],
    'next' => $result['next'] ?? null,
    'data' => $result['data'] ?? null,
], JSON_UNESCAPED_SLASHES);
