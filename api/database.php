<?php

    function createConnection(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "aplikasi-kasir";
        $port = "33061";
        $connection = new mysqli($servername, $username, $password, $database, $port);

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        return $connection;
    }

?>