<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to retrieve existing users from the database
$sql = "SELECT `username` FROM `volunteer`";
$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}

if ($result->num_rows > 0) {
    $users = array();

    while($row = $result->fetch_assoc()) {
        $users[] = array('username' => $row['username']);
    }

    echo json_encode(['users' => $users]);
} else {
    echo json_encode(['users' => []]);
}

// Close the database connection
$con->close();
?>
