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
$username = isset($data->username) ? $data->username : '';
$email = isset($data->email) ? $data->email : '';
$password = isset($data->password) ? $data->password : '';


// Clean user input
$username = $connection->real_escape_string($username);
$password = $connection->real_escape_string($password);
$email = $connection->real_escape_string($email);


function SignUp($username, $email, $password) {
    
    if ($username === '' || $password === '' || $email === '') {
        http_response_code(400); // use appropriate status
        echo json_encode(array("error" => "Fields 'username', 'email', and 'password' are required"));
        return;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid email '$email'"));
        return;
    }
    
    global $connection;
    $passwordHash = md5($password);

    $query = "INSERT INTO `UserData`
                (`username`, `email`, `password`) 
                VALUES
                ('$username', '$email', '$passwordHash')";
 
    if ($connection->query($query)) {
        
        $user = array(
            "id" => (int) $connection->insert_id,
            "username" => $username,
            "email" => $email,
        );
        
        http_response_code(200);
        echo json_encode($user, JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(500); // use appropriate status
        echo json_encode(array("error" => $connection->error));
    }

}

// mail("vhernandezcastro@gmail.com", "test_signup_android", json_encode($data));
SignUp($username, $email, $password);
