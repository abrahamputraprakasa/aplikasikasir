<?php
    $password = $_GET['password'];
    $hashedPassword = hash('sha256', $password);
    echo $hashedPassword;
?>