<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "web2024");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function handleUpdateData($data) {
    global $conn;
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
            echo json_encode(['error' => 'Invalid table name']);
            break;
    }
}

function handleUpdateItem($data) {
    global $conn;

    if (isset($data['item_id'], $data['name'], $data['category'], $data['quantity'])) {
        $itemID = $data['item_id'];
        $name = mysqli_real_escape_string($conn, $data['name']);
        $category = mysqli_real_escape_string($conn, $data['category']);
        $quantity = (int) $data['quantity'];

        $updateQuery = "UPDATE items SET name = '$name', category = '$category', quantity = $quantity WHERE item_id = $itemID";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            handleCommitAndResponse(true, 'Item updated successfully');
        } else {
            handleRollbackAndResponse(false, 'Error updating item: ' . mysqli_error($conn));
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for item update']);
    }
}

function handleUpdateCategory($data) {
    global $conn;

    if (isset($data['category_id'], $data['category_name'])) {
        $categoryID = $data['category_id'];
        $categoryName = mysqli_real_escape_string($conn, $data['category_name']);

        $updateQuery = "UPDATE item_categories SET category_name = '$categoryName' WHERE category_id = $categoryID";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            handleCommitAndResponse(true, 'Category updated successfully');
        } else {
            handleRollbackAndResponse(false, 'Error updating category: ' . mysqli_error($conn));
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for category update']);
    }
}

function handleUpdateDetail($data) {
    global $conn;

    if (isset($data['it_id'], $data['detail_name'], $data['detail_value'])) {
        $itID = $data['it_id'];
        $detailName = mysqli_real_escape_string($conn, $data['detail_name']);
        $detailValue = mysqli_real_escape_string($conn, $data['detail_value']);

        $updateQuery = "UPDATE item_details SET detail_name = '$detailName', detail_value = '$detailValue' WHERE it_id = $itID";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            handleCommitAndResponse(true, 'Detail updated successfully');
        } else {
            handleRollbackAndResponse(false, 'Error updating detail: ' . mysqli_error($conn));
        }
    } else {
        echo json_encode(['error' => 'Missing required parameters for detail update']);
    }
}

function handleCommitAndResponse($success, $message) {
    global $conn;
    mysqli_commit($conn);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
}

function handleRollbackAndResponse($success, $message) {
    global $conn;
    mysqli_rollback($conn);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    if (isset($requestData['action'], $requestData['tableName'], $requestData['data'])) {
        if ($requestData['action'] === 'updateData') {
            mysqli_autocommit($conn, false); // Start transaction
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
