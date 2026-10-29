
<?php
require 'db.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$collector_id = 1; // Temporary test collector ID

$sql = "SELECT latitude, longitude, updated_at
        FROM truck_locations
        WHERE collector_id = ?
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$collector_id]);

$location = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($location ?: [
    'latitude' => null,
    'longitude' => null,
    'updated_at' => null
]);
?>
