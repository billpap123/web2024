<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the category parameter from the request
$categories = isset($_GET['categories']) ? $_GET['categories'] : '';

// Convert the categories string to an array
$categoriesArray = explode(',', $categories);

// Escape and prepare the categories for the SQL query
$escapedCategories = array_map(function ($category) use ($con) {
    return mysqli_real_escape_string($con, $category);
}, $categoriesArray);

// Query to retrieve existing items from the database with optional category filtering
$sql = "SELECT `name`, `quantity` FROM `items` WHERE `quantity` > 0";

// Add category filtering if categories are specified
if (!empty($escapedCategories)) {
    $categoryFilter = implode("','", $escapedCategories);
    $sql .= " AND `category` IN ('$categoryFilter')";
}

$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}

$items = array();

while ($row = $result->fetch_assoc()) {
    $items[] = array('name' => $row['name'], 'quantity' => $row['quantity']);
}

echo json_encode(['items' => $items]);

// Close the database connection
$con->close();
?>
