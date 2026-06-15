<?php
// get_cities.php
require_once 'db_config.php';

if (isset($_GET['state_id'])) {
    $state_id = intval($_GET['state_id']);
    
    $stmt = $conn->prepare("SELECT id, name FROM cities WHERE state_id = ? ORDER BY name ASC");
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
    
    // 将结果以 JSON 格式返回给前端
    header('Content-Type: application/json');
    echo json_encode($cities);
}
?>