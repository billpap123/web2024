<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die(json_encode(['error' => "Connection failed: " . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file is uploaded
    if (isset($_FILES['jsonFile']) && $_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['jsonFile'];

        $jsonData = file_get_contents($uploadedFile['tmp_name']);

        if ($jsonData === false) {
            die(json_encode(['error' => "Failed to read JSON data from the uploaded file"]));
        }

        // Decode JSON data into PHP array
        $data = json_decode($jsonData, true);

        if ($data === null) {
            die(json_encode(['error' => "Invalid JSON format in the uploaded file"]));
        }

        // Insert data into the item_categories table
        $insertItemCategoryQuery = "INSERT INTO item_categories (category_id, category_name) VALUES (?, ?)";
        $stmtCategory = $con->prepare($insertItemCategoryQuery);

        if ($stmtCategory) {
            try {
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

                if ($stmtItem && $stmtDetail) {
                    foreach ($data['items'] as $item) {
                        $stmtItem->bind_param("isi", $item['id'], $item['name'], $item['category']);
                        $stmtItem->execute();

                        foreach ($item['details'] as $detail) {
                            $stmtDetail->bind_param("iss", $item['id'], $detail['detail_name'], $detail['detail_value']);
                            $stmtDetail->execute();
                        }
                    }

                    echo json_encode(['message' => "Data inserted successfully."]);
                } else {
                    echo json_encode(['error' => "Prepare statement error: " . $con->error]);
                }

                $stmtItem->close();
                $stmtDetail->close();
            } catch (Exception $e) {
                echo json_encode(['error' => "An error occurred: " . $e->getMessage()]);
                // Log the error to a file or database for further investigation
            }
        } else {
            echo json_encode(['error' => "Prepare statement error: " . $con->error]);
        }

        // Close the database connection
        $con->close();
    } else {
        echo json_encode(['error' => "Invalid file upload or missing file."]);
    }
} else {
    echo json_encode(['error' => "Invalid request method."]);
}
?>
