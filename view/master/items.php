<?php
    include('../header.php');
    if (!isset($_SESSION["email"])) {
        header("Location: index.php");
        die();
    }
?>

    <div class="container col-lg-6 my-5 center">
        <h2>Barang-Barang</h2>
        <div class="px-0">

        </div>
    </div>

<?php
    include('../footer.php');
?>