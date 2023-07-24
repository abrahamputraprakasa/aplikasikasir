<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "aplikasi-kasir";
    $port = "33061";

    //MySQLi
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>