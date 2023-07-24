<?php
    include('view/header.php');
    if (!isset($_SESSION["email"])) {
        header("Location: index.php");
        die();
    }
?>

    <div class="container col-lg-6 my-5 center">
        <h1>Selamat datang, <?= $_SESSION["email"] ?></h1>
        <div class="px-0">

        </div>
    </div>

<?php
    include('view/footer.php');
?>