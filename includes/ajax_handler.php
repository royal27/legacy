<?php
// Set the content type to JSON
header('Content-Type: application/json');

// This is a very simple AJAX handler.
// In a real application, you would include the core files
// to access the database and other functions.

// You could have a 'action' parameter to decide what to do.
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

// Encode the response array to JSON and output it.
echo json_encode($response);
exit;
?>
