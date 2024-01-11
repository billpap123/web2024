<?php
// Your database connection code here
header('Content-Type: application/json');

$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the form data
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate form data
if (empty($username) || empty($password)) {
    echo json_encode(['error' => 'Invalid data received']);
    exit;
}

// Escape and prepare the data for the SQL query
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

// Query to insert the new user into the database
$sql = "INSERT INTO `volunteer` (`username`, `password`) VALUES ('$username', '$hashedPassword')";

$result = $con->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Error creating user: ' . $con->error]);
} else {
    echo json_encode(['message' => 'User created successfully']);
}

// Close the database connection
$con->close();
?>
