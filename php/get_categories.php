<?php
$conn = mysqli_connect("localhost", "root", "", "web2024");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM item_categories");

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$categories = array();

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = array(
        'category_id' => $row['category_id'],
        'category_name' => $row['category_name']
    );
}

// Send the appropriate Content-Type header before echoing the JSON
header('Content-Type: application/json');

// Output only the JSON data
echo json_encode($categories);

mysqli_close($conn);
?>
