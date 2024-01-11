<?php
// Database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set JSON content type header
header('Content-Type: application/json');

// Receive data from the client
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['stor_long']) && isset($data['stor_lat'])) {
    $long = mysqli_real_escape_string($con, $data['stor_long']);
    $lat = mysqli_real_escape_string($con, $data['stor_lat']);

    

    // Insert user into the database
    $stmt = $con->prepare("INSERT INTO storage (stor_long, stor_lat) VALUES (?, ?)");
    $stmt->bind_param("dd", $long, $lat);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Coordinates stored successfully']);
    } else {
        // Log the detailed error for debugging
        error_log('Error storing coordinates: ' . $stmt->error);

        // Provide a generic error message to the client
        echo json_encode(['message' => 'Error storing coordinates. Please try again.']);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo json_encode(['message' => 'Invalid data received']);
}

// Close the database connection
$con->close();
?>
