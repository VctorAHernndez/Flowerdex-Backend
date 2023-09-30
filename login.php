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
$email = isset($data->email) ? $data->email : '';
$password = isset($data->password) ? $data->password : '';


// Clean user input
$email = $connection->real_escape_string($email);
$password = $connection->real_escape_string($password);


function Login($email, $password) {

    if ($email === '' || $password === '') {
        http_response_code(400); // use appropriate status
        echo json_encode(array("error" => "Fields 'email' and 'password' are required"));
        return;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid email '$email'"));
        return;
    }

    global $connection;
    $passwordHash = md5($password);

    $query = "SELECT * 
                FROM UserData U
                WHERE U.email = '$email'
                AND U.password = '$passwordHash'";
    
    if ($result = $connection->query($query)) {
        if ($row = $result->fetch_object()) {
            
            $user = array(
                "id" => (int) $row->id,
                "username" => $row->username,
                "email" => $row->email,
            );      
            
            http_response_code(200);
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
            
        } else {
            http_response_code(400); // use appropriate status code
            echo json_encode(array("error" => "No user with given credentials"));
        }
    } else {
        http_response_code(500); // use appropriate status code
        echo json_encode(array("error" => $connection->error));
    }

}

// mail("vhernandezcastro@gmail.com", "test_login_android", json_encode($data));
Login($email, $password);
