<?php

require_once('config.php');


// Filter unsupported HTTP requests
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(400); // use appropriate status
    echo json_encode(array("error" => "Endpoint only supports GET requests"));
    exit();
}


// Get user input
$userID = isset($_GET['user_id']) ? $_GET['user_id'] : '';


// Clean user input
$userID = $connection->real_escape_string($userID);


function fetchSingleFlower($flowerID) {

    global $TREFFLE_BASE_URL, $TREFFLE_LIST_PATH, $TREFFLE_TOKEN;

    $endpoint = $TREFFLE_BASE_URL . $TREFFLE_LIST_PATH . "/$flowerID";
    $queryParams = array("token" => $TREFFLE_TOKEN);
    
    $curl = curl_init();
    $url = sprintf("%s?%s", $endpoint, http_build_query($queryParams));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($curl);
    $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    curl_close($curl);

    return array("data" => json_decode($response), "code" => $http_response_code);
    
}


function listHistory($userID) {

    if ($userID == '') {
        http_response_code(400); // use appropriate status code
        echo json_encode(array("error" => "Field 'user_id' is required"));
        return;
    }

    global $connection;

    $query = "SELECT * 
                FROM UserHistory AS H
                WHERE H.user_id = '$userID';";

    if ($result = $connection->query($query)) {


        // Create flower container
        $flowers = [];


        // Loop through user's flowers
        while($row = $result->fetch_assoc()) {
            
            // Query API
            $apiOutput  = fetchSingleFlower($row['flower_id']);
            $data = $apiOutput['data'];
            $code = $apiOutput['code'];
            
            // Handle API errors        
            if ($code >= 300 || $code < 200) {
                http_response_code($code);
                echo json_encode(array("error" => "Unknown error occurred with Treffle API (Code: $code)"));
                return;
            } elseif (isset($data->error) && $data->error == "true") {
                http_response_code(400);
                $msg = isset($data->message) ? $data->message : $data->messages;
                echo json_encode(array("error" => $msg));
                return;
            }
            
            // Extract flower
            $flower = $data->data;
            $flower->isFavorite = $row['in_wishlist'] == "1" ? true : false;
            $flower->hasBeenFound = $row['has_been_found'] == "1" ? true : false;
            
            // Append flower
            $flowers[] = $flower;
            
        }
        
        
        // Return response
        http_response_code(200);
        $response = new stdClass;
        $response->data = $flowers;
        echo json_encode($response);


    } else {
        http_response_code(500); // use appropriate status code
        echo json_encode(array("error" => $connection->error));
    }

}


listHistory($userID);
