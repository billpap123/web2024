<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "web2024");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['table'])) {
    $table = $_GET['table'];

    switch ($table) { 
        case 'items': 
            fetchItems();
            break;
        case 'item_categories':
            fetchCategories();
            break;
        case 'item_details':
            fetchDetails();
            break;
        default:
            // Handle unknown table or provide an error response
            echo json_encode(['error' => 'Unknown table']);
            break;
    }
} else {
    // Handle case where "table" parameter is not provided
    echo json_encode(['error' => 'Table parameter missing']);
}

// Function to fetch items data
function fetchItems() {
    global $con;

    $query = "SELECT * FROM items";
    $result = $con->query($query);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => $con->error]);
    }
}

// Function to fetch item categories data
function fetchCategories() {
    global $con;

    $query = "SELECT category_name FROM item_categories";
    $result = $con->query($query);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => $con->error]);
    }
}

// Function to fetch item details data
function fetchDetails() {
    global $con;

    $query = "SELECT * FROM item_details";
    $result = $con->query($query);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => $con->error]);
    }
}
?>
