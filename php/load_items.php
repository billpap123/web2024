<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Load JSON data from file
$jsonUrl = 'http://usidas.ceid.upatras.gr/web/2023/export.php';
$jsonData = file_get_contents($jsonUrl);

if ($jsonData === false) {
    die(json_encode(['error' => 'Failed to fetch JSON data from the URL']));
}

// Decode JSON data into PHP array
$data = json_decode($jsonData, true);

try {
    // Begin transaction
    mysqli_begin_transaction($con);

    // Insert data into the item_categories table
    $insertItemCategoryQuery = "INSERT INTO item_categories (category_id, category_name) VALUES (?, ?)";
    $stmtCategory = $con->prepare($insertItemCategoryQuery);

    foreach ($data['categories'] as $category) {
        $stmtCategory->bind_param("is", $category['id'], $category['category_name']);
        $stmtCategory->execute();
    }

    $stmtCategory->close();

    // Insert data into the items and item_details tables
    $insertItemQuery = "INSERT INTO items (item_id, name, category) VALUES (?, ?, ?)";
    $insertItemDetailQuery = "INSERT INTO item_details (it_id, detail_name, detail_value) VALUES (?, ?, ?)";

    $stmtItem = $con->prepare($insertItemQuery);
    $stmtDetail = $con->prepare($insertItemDetailQuery);

    foreach ($data['items'] as $item) {
        $stmtItem->bind_param("isi", $item['id'], $item['name'], $item['category']);
        $stmtItem->execute();

        foreach ($item['details'] as $detail) {
            $stmtDetail->bind_param("iss", $item['id'], $detail['detail_name'], $detail['detail_value']);
            $stmtDetail->execute();
        }
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode(['message' => 'Data inserted successfully']);
} catch (Exception $e) {
    // Rollback changes on error
    mysqli_rollback($con);

    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    // Log the error to a file or database for further investigation
} finally {
    // Close statements and the database connection
    $stmtItem->close();
    $stmtDetail->close();
    $con->close();
}
?>