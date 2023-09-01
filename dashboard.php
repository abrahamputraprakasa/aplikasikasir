<?php
include('view/header.php');
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    die();
}
?>

<div class="container col-lg-6 my-5 center">
    <div class="container my-5">
        <h1>Selamat datang, <?= $_SESSION["email"] ?></h1>
        <div class="col-lg-8 px-0">
            <div class="fs-5">Penjualan Bulan Ini</div>
            <p id="thisMonthSelling"></p>

            <div class="fs-5">Bulan Lalu</div>
            <p id="lastMonthSelling"></p>

            <hr class="col-1 my-4">

            <a href="/aplikasi-kasir/view/transaction/transaction.php" class="btn btn-primary">Buat Transaksi</a>
            <a href="/aplikasi-kasir/view/transaction/history.php" class="btn btn-secondary">Riwayat</a>
        </div>
    </div>
</div>
<script>
    function getThisMonthSelling() {
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/transaction/transaction.php',
            data: {
                action: 'thisMonthSelling',
            },
            success: function(data) {
                $("#thisMonthSelling").html('Rp' + parseInt(data.sum_total_price ?? 0).toLocaleString('id-ID'));
            },
            dataType: 'json'
        });
    }

    function getLastMonthSelling() {
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/transaction/transaction.php',
            data: {
                action: 'lastMonthSelling',
            },
            success: function(data) {
                $("#lastMonthSelling").html('Rp' + parseInt(data.sum_total_price ?? 0).toLocaleString('id-ID'));
            },
            dataType: 'json'
        });
    }

    $(document).ready(function() {
        getThisMonthSelling();
        getLastMonthSelling();
    });
</script>
<?php
include('view/footer.php');
?>