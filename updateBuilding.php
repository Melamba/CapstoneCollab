<?php
require 'CapstoneSample_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $logId = isset($_POST['log_id'])
        ? (int) $_POST['log_id']
        : 0;

    $building = $_POST['building'] ?? '';

    if ($logId > 0 && $building !== '') {

        $stmt = $pdo->prepare(
            "INSERT INTO location (log_id, building)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE building = VALUES(building)"
        );

        $stmt->execute([$logId, $building]);

        echo "Building updated successfully";
    } else {
        http_response_code(400);
        echo "Invalid log ID or building";
    }
}
?>