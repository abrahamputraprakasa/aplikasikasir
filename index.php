<?php
    include('view/header.php');
    if (isset($_SESSION["email"]) && $_SESSION["email"]) {
        header("Location: dashboard.php");
        die();
    }
?>

<div class="container col-lg-6 my-5 center">
    <h1>Selamat datang, di Aplikasi Kasir</h1>
    <div class="px-0">
        <p class="fs-5">Silakan login terlebih dahulu</p>
        <form class="d-flex flex-column" role="search" method="POST" action="api/login.php">
            <div class="my-1">
                <input class="form-control me-2 my-1" type="text" placeholder="Email" name="email" aria-label="Search" required autocomplete="off">
                <input class="form-control me-2" type="password" placeholder="Password" name="password" aria-label="Search" required autocomplete="off">
            </div>
            <div class="text-center">
                <button class="btn btn-outline-success w-50" type="submit">Login</button>
            </div>
        </form>
        <?php if (isset($_SESSION["error_message"])) : ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo $_SESSION["error_message"];
                unset($_SESSION["error_message"]);
                ?>
            </div>
        <?php endif ?>
    </div>
</div>

<?php
    include('view/footer.php');
?>