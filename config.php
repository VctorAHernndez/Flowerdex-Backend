<?php 

$TREFFLE_BASE_URL = "https://trefle.io";
$TREFFLE_LIST_PATH = "/api/v1/plants";
$TREFFLE_SEARCH_PATH = "/api/v1/plants/search";
$TREFFLE_TOKEN = "hg6M-l4XhrgVgn2A-qZC6KKMrVPMUuCffVfDgDPtc0I";

$DB_HOST = "localhost";
$DB_USER = "Floradex";
$DB_PASSWORD = "13CUdcMOXiybeZuj";
$DB_NAME = "Floradex";

$connection = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

if ($connection->connect_errno) {
    echo "Failed to connect to MySQL: " . $connection->connect_error;
    exit();
}

$connection->set_charset('utf8');
