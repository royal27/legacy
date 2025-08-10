<?php
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = [];

switch ($action) {
    case 'get_server_time':
        $response = [
            'success' => true,
            'message' => 'Data fetched successfully!',
            'data' => [
                'server_time' => date('Y-m-d H:i:s'),
                'random_number' => rand(1, 100)
            ]
        ];
        break;
    default:
        $response = [
            'success' => false,
            'message' => 'Invalid action specified.'
        ];
        break;
}

echo json_encode($response);
exit;
?>
