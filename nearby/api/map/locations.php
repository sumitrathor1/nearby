<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/helpers/location.php';

$type = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
$maxPrice = isset($_GET['max_price']) ? (int) $_GET['max_price'] : null;
$search = isset($_GET['q']) ? trim((string) $_GET['q']) : null;

$locations = nearby_get_verified_locations();
$filtered = nearby_filter_locations($locations, [
    'type' => $type !== '' ? $type : null,
    'max_price' => $maxPrice > 0 ? $maxPrice : null,
    'q' => $search !== '' ? $search : null
]);

$response = [
    'success' => true,
    'data' => nearby_serialize_locations($filtered),
    'meta' => [
        'count' => count($filtered),
        'filters' => array_filter([
            'type' => $type !== '' ? $type : null,
            'max_price' => $maxPrice > 0 ? $maxPrice : null,
            'q' => $search !== '' ? $search : null
        ])
    ]
];

echo json_encode($response);
