<?php

require_once('config.php');


// Filter unsupported HTTP requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(400); // use appropriate status
    echo json_encode(array("error" => "Endpoint only supports POST requests"));
    exit();
}


// Get input
$json = file_get_contents('php://input');
$data = json_decode($json);
$userID = isset($data->user_id) ? $data->user_id : '';
$flowerID = isset($data->flower_id) ? $data->flower_id : '';


// Clean user input
$flowerID = $connection->real_escape_string($flowerID);
$userID = $connection->real_escape_string($userID);


function removeFoundFlower($userID, $flowerID) {

    if ($userID == '' || $flowerID == '') {
        http_response_code(400); // use appropriate status code
        echo json_encode(array("error" => "Fields 'user_id' and 'flower_id' are requried"));
        return;
    }

    global $connection;
    
    $query = "SELECT * FROM UserHistory AS H WHERE H.user_id = '$userID' AND H.flower_id = '$flowerID';";
    
    if ($result = $connection->query($query)) {
        if ($result->num_rows > 0) {
        
            $query = "UPDATE UserHistory AS H SET H.has_been_found = 0 WHERE H.user_id = '$userID' AND H.flower_id = '$flowerID';";
            
            if ($connection->query($query)) {
                http_response_code(200);
            } else {
                http_response_code(500); // use appropriate status code
                echo json_encode(array("error" => $connection->error));
            }
        
        } else {
        
            // Theres nothing to remove, since it doesn't exist
            http_response_code(200);
            
        }
    } else {
        http_response_code(500); // use appropriate status code
        echo json_encode(array("error" => $connection->error));
    }

}

// mail("vhernandezcastro@gmail.com", "test_remove_favorite_android", json_encode($data));
removeFoundFlower($userID, $flowerID);