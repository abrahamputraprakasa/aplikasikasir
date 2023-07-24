<?php
    include('database.php');
    session_start();

    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = hash('sha256', $password);

    $stmt = $conn->prepare("SELECT id, `name`, email FROM users WHERE `email`=? AND `password`=?");
    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $user = $result->fetch_assoc();

    $conn->close();
    $redirectTo = "";
    if ($user) {
        // redirect ke halaman utama;
        $_SESSION["email"] = $email;
        $redirectTo = "../dashboard.php";
    } else {
        $_SESSION["error_message"] = "Email atau password salah";
        $redirectTo = "../index.php";
    }
    header("Location: $redirectTo");
    // echo json_encode($user);
?>