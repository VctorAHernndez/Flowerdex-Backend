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
$q = isset($_GET['q']) ? $_GET['q'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';
$edible = isset($_GET['edible']) ? $_GET['edible'] : '';
$vegetable = isset($_GET['vegetable']) ? $_GET['vegetable'] : '';
$scientificName = isset($_GET['scientific_name']) ? $_GET['scientific_name'] : '';
$growthMonths = isset($_GET['growth_months']) ? $_GET['growth_months'] : '';
$bloomMonths = isset($_GET['bloom_months']) ? $_GET['bloom_months'] : '';
$color = isset($_GET['flower_color']) ? $_GET['flower_color'] : '';


// Clean user input
$userID = $connection->real_escape_string($userID);


function prepareQueryParams($q = '', $edible = '', $vegetable = '', $scientificName = '', $growthMonths = '', $bloomMonths = '', $color = '', $page = '') {

    // QUERY PARAMETERS
    // - q: String
    // - page: Int
    // - edible: Bool
    // - vegetable: Bool
    // - scientific_name: String
    // - growth_months: Int
    // - bloom_months: Int

    global $TREFFLE_TOKEN;

    // Define preliminar round of params
    $queryParams = array(
        "token" => $TREFFLE_TOKEN,
        "page" => $page ? $page : '1',
        "filter[edible]" => $edible ? $edible : 'false',
        "filter[vegetable]" => $vegetable ? $vegetable : 'false',
    );


    // Set the rest of the params
    if($q) {
        $queryParams['q'] = $q;
    }

    if($scientificName) {
        $queryParams['filter[scientific_name]'] = $scientificName;
    }

    if($growthMonths) {
        $queryParams['filter[growth_months]'] = $growthMonths;
    }

    if($bloomMonths) {
        $queryParams['filter[bloom_months]'] = $bloomMonths;
    }
    
    if($color) {
        $queryParams['filter[flower_color]'] = $color;
    }

    return $queryParams;

}


function callAPI($queryParams) {

    global $TREFFLE_BASE_URL, $TREFFLE_LIST_PATH, $TREFFLE_SEARCH_PATH;

    if(isset($queryParams['q']) && $queryParams['q'] != '') {
        $endpoint = $TREFFLE_BASE_URL . $TREFFLE_SEARCH_PATH;
    } else {
        $endpoint = $TREFFLE_BASE_URL . $TREFFLE_LIST_PATH;
    }
    
    $curl = curl_init();
    $url = sprintf("%s?%s", $endpoint, http_build_query($queryParams));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($curl);
    $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    curl_close($curl);

    return array("data" => json_decode($response), "code" => $http_response_code);
    
}


function listFlowers($userID, $q = '', $edible = '', $vegetable = '', $scientificName = '', $growthMonths = '', $bloomMonths = '', $color = '', $page = '') {

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


        // Query API
        $queryParams = prepareQueryParams($q, $edible, $vegetable, $scientificName, $growthMonths, $bloomMonths, $color, $page);
        $apiOutput = callAPI($queryParams);
        $data = $apiOutput['data'];
        $code = $apiOutput['code'];


        // Handle API errors        
        if ($code >= 300 || $code < 200) {
            http_response_code(500);
            echo json_encode(array("error" => "Unknown error occurred with Treffle API (Code: $code)"));
            return;
        } elseif (isset($data->error) && $data->error == "true") {
            http_response_code(400);
            echo json_encode(array("error" => isset($data->messages) ? $data->messages : $data->message));
            return;
        }


        // Extract flowers
        $flowers = $data->data;

        
        // Initialize all both fields to false for all flowers
        foreach($flowers as $f) {
            $f->isFavorite = false;
            $f->hasBeenFound = false;
        }       


        // Loop through each "favorited"/"marked" flower and update accordingly
        // NOTE: WOULD'VE BEEN EASIER IF F WERE A DICT :(
        while($row = $result->fetch_assoc()) {
            foreach($flowers as $f) {
                if($f->id == $row['flower_id']) {
                    $f->isFavorite = $row['in_wishlist'] == "1" ? true : false;
                    $f->hasBeenFound = $row['has_been_found'] == "1" ? true : false;
                }
            }
        }
        
        
        // Return response
        http_response_code(200);
        $response = new stdClass;
        $response->data = $flowers;
        $response->links = $data->links;
        $response->meta = $data->meta;
        echo json_encode($response);


    } else {
        http_response_code(500); // use appropriate status code
        echo json_encode(array("error" => $connection->error));
    }

}


listFlowers($userID, $q, $edible, $vegetable, $scientificName, $growthMonths, $bloomMonths, $color, $page);
