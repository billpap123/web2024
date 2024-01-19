<?php
$conn = mysqli_connect("localhost", "root", "", "web2024");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$item_id = mysqli_real_escape_string($conn, $_GET['item_id']);

$result = mysqli_query($conn, "SELECT category FROM items WHERE item_id='$item_id'");

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$category_id = ($row) ? $row['category'] : '';

echo $category_id;

mysqli_close($conn);
?>
