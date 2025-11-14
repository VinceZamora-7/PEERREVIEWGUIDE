<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Always return JSON
header('Content-Type: application/json');

// Database credentials (consider using environment variables in production)
$host = "sql103.infinityfree.com";
$username = "if0_40271114";
$password = "QdO20m5hR4JbOHe";
$dbname = "if0_40271114_peer_review_db";

// Create MySQL connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Database connection failed: " . $mysqli->connect_error
    ]);
    exit;
}

// Validate required POST fields
if (!isset($_POST['pr_id'], $_POST['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing PRID or status.']);
    exit;
}

$pr_id = trim($_POST['pr_id']);
$status = trim($_POST['status']);

// Check for empty values
if (empty($pr_id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'PRID and Status cannot be empty.']);
    exit;
}

// ✅ Allowable status values
$valid_statuses = [
    'Completed - Valid',
    'Completed - Invalid',
    'Pending',
    'Pending - Builder Notified'
];

// Validate status
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "Invalid status value: '$status'.",
        'allowed_statuses' => $valid_statuses
    ]);
    exit;
}

// Prepare SQL query
$stmt = $mysqli->prepare("UPDATE pr_submissions SET status = ? WHERE pr_id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement.']);
    exit;
}

// Execute query
$stmt->bind_param("ss", $status, $pr_id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => "Status for PRID '$pr_id' updated to '$status'."
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update status in the database.'
    ]);
}

// Cleanup
$stmt->close();
$mysqli->close();
?>
