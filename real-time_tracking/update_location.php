
<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Temporary test: use collector ID 1.
// We will connect this to your actual collector login later.
$collector_id = 1;

$data = json_decode(file_get_contents("php://input"), true);

$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;

if (
    !is_numeric($latitude) ||
    !is_numeric($longitude) ||
    $latitude < -90 || $latitude > 90 ||
    $longitude < -180 || $longitude > 180
) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid GPS coordinates"]);
    exit;
}

$sql = "INSERT INTO truck_locations
        (collector_id, latitude, longitude)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        latitude = VALUES(latitude),
        longitude = VALUES(longitude),
        updated_at = CURRENT_TIMESTAMP";

$stmt = $pdo->prepare($sql);
$stmt->execute([$collector_id, $latitude, $longitude]);

echo json_encode(["success" => true]);
?>
