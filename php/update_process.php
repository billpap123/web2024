<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "web2024");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Fetch the corresponding category from the 'items' table
$sql_fetch_category = "SELECT category FROM items WHERE item_id=?";
$stmt_fetch_category = mysqli_prepare($conn, $sql_fetch_category);
mysqli_stmt_bind_param($stmt_fetch_category, "i", $item_id);
mysqli_stmt_execute($stmt_fetch_category);
$result_fetch_category = mysqli_stmt_get_result($stmt_fetch_category);

if (!$result_fetch_category) {
    $response['success'] = false;
    $response['message'] = "Error fetching category: " . mysqli_error($conn);
    // You can add more details or log the error as needed
} else {
    // ... Rest of your code
}

// Get form data
$item_id = mysqli_real_escape_string($conn, $_POST['item_id'] ?? '');
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$quantity = mysqli_real_escape_string($conn, $_POST['quantity'] ?? '');
$category_id = mysqli_real_escape_string($conn, $_POST['category_id'] ?? '');
$category_name = mysqli_real_escape_string($conn, $_POST['category_name'] ?? '');
$detail_name = mysqli_real_escape_string($conn, $_POST['detail_name'] ?? '');
$detail_value = mysqli_real_escape_string($conn, $_POST['detail_value'] ?? '');
$detail_id = mysqli_real_escape_string($conn, $_POST['detail_id'] ?? '');


// Fetch the corresponding category from the 'items' table
$sql_fetch_category = "SELECT category FROM items WHERE item_id=?";
$stmt_fetch_category = mysqli_prepare($conn, $sql_fetch_category);
mysqli_stmt_bind_param($stmt_fetch_category, "i", $item_id);
mysqli_stmt_execute($stmt_fetch_category);
$result_fetch_category = mysqli_stmt_get_result($stmt_fetch_category);

$response = array(); // Create an array to hold the response

if ($row_fetch_category = mysqli_fetch_assoc($result_fetch_category)) {
    $db_category_name = $row_fetch_category['category'];

    // Perform the update for 'items' table
    $sql_items = "UPDATE items SET name=?, quantity=?, category=? WHERE item_id=?";
    $stmt_items = mysqli_prepare($conn, $sql_items);
    mysqli_stmt_bind_param($stmt_items, "ssii", $name, $quantity, $category_id, $item_id);
    $result_items = mysqli_stmt_execute($stmt_items);

 // Perform the update for 'item_details' table
$sql_details = "UPDATE item_details SET detail_name=?, detail_value=? WHERE detail_id=?";
$stmt_details = mysqli_prepare($conn, $sql_details);
mysqli_stmt_bind_param($stmt_details, "ssi", $detail_name, $detail_value, $detail_id);
$result_details = mysqli_stmt_execute($stmt_details);

    // Perform the update for 'item_categories' table
    $sql_categories = "UPDATE item_categories SET category_name=? WHERE category_id=?";
    $stmt_categories = mysqli_prepare($conn, $sql_categories);
    mysqli_stmt_bind_param($stmt_categories, "si", $category_name, $category_id);
    $result_categories = mysqli_stmt_execute($stmt_categories);

    // Check if all updates were successful
    if ($result_items && $result_details && $result_categories) {
        mysqli_commit($conn);
        $response['success'] = true;
        $response['message'] = "Items updated successfully";
    } else {
        mysqli_rollback($conn);
        $response['success'] = false;
        $response['message'] = "Error updating items: " . mysqli_error($conn);
    }
} else {
    $response['success'] = false;
    $response['message'] = "Error fetching category: " . mysqli_error($conn);
}

mysqli_close($conn);

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
