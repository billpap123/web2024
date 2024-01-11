<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

function handleUpdateData($data) {
    global $con;
    switch (strtolower($data['tableName'])) {
        case 'items':
            handleUpdateItem($data['data']);
            break;
        case 'item_categories':
            handleUpdateCategory($data['data']);
            break;
        case 'item_details':
            handleUpdateDetail($data['data']);
            break;
        default:
            echo json_encode(['error' => 'Invalid table name'.$table]);
            break;
    }
    
}

function handleUpdateItem($data) {
    global $con;

    if (isset($data['item_id'], $data['name'], $data['category'], $data['quantity'])) {
        $itemID = $data['item_id'];
        $name = mysqli_real_escape_string($con, $data['name']);
        $category = mysqli_real_escape_string($con, $data['category']);
        $quantity = (int) $data['quantity'];

        $updateQuery = "UPDATE items SET name = '$name', category = '$category', quantity = $quantity WHERE item_id = $itemID";
        $updateResult = mysqli_query($con, $updateQuery);

        if ($updateResult) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Item updated successfully']);        } else {
            echo json_encode(['error' => 'Error updating item: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for item update']);
    }
}

function handleUpdateCategory($data) {
    global $con;

    if (isset($data['category_id'], $data['category_name'])) {
        $categoryID = $data['category_id'];
        $categoryName = mysqli_real_escape_string($con, $data['category_name']);

        $updateQuery = "UPDATE item_categories SET category_name = '$categoryName' WHERE category_id = $categoryID";
        $updateResult = mysqli_query($con, $updateQuery);

        if ($updateResult) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['error' => 'Error updating category: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for category update']);
    }
}

function handleUpdateDetail($data) {
    global $con;

    if (isset($data['it_id'], $data['detail_name'], $data['detail_value'])) {
        $itID = $data['it_id'];
        $detailName = mysqli_real_escape_string($con, $data['detail_name']);
        $detailValue = mysqli_real_escape_string($con, $data['detail_value']);

        $updateQuery = "UPDATE item_details SET detail_name = '$detailName', detail_value = '$detailValue' WHERE it_id = $itID";
        $updateResult = mysqli_query($con, $updateQuery);

        if ($updateResult) {
            echo json_encode(['success' => true, 'message' => 'Detail updated successfully']);
        } else {
            echo json_encode(['error' => 'Error updating detail: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for detail update']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    if (isset($requestData['action'], $requestData['tableName'], $requestData['data'])) {
        if ($requestData['action'] === 'updateData') {
            handleUpdateData($requestData);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request parameters']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
