<?php

function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "bd_train";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}