<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <style>
        body {
            background-color: #f0f0f0;

        }

        .printarea {
            max-width: 58mm;
            background-color: #fff;
            border: 1px solid black;
        }

        table {
            width: 100%;
        }

        .container {
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="printarea">
        <div class="container">
            <center>Aplikasi Kasir</center>
            <br>
            <div id="invoiceNumber"></div>
            <div id="dateTime"><?= date('d-M-y h:i:s') ?></div>
            <div id="kasir"></div>
            <br>
            <table id="tableku">
            </table>
        </div>
    </div>
    <script>
        var requestId = "<?php echo $_GET['id'] ?? 0 ?>";
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/transaction/invoice.php',
            data: {
                id: requestId
            },
            success: function(data) {
                console.log('data', data);
                $("#invoiceNumber").html(data.transaction.invoice_number);
                $("#kasir").html(data.transaction.user_name);
                data.transaction_detail.forEach(element => {
                    const subtotal = (parseInt(element.quantity) * parseInt(element.item_price))
                    const subtotalString = subtotal.toLocaleString('id-ID');
                    $('#tableku').append('<tr><td colspan="3">' + element.item_name + '</td></tr>');
                    $('#tableku').append('<tr><td align="right">' + element.item_price.toLocaleString('id-ID') + '</td><td align="right">x' + element.quantity + '</td><td align="right">= Rp' + subtotalString + '</td></tr>');
                });
                $('#tableku').append('<tr><td><b>Total</b></td><td></td><td align="right"><b>Rp' + data.transaction.total_price.toLocaleString('id-ID') + '</b></td>');
            },
            dataType: 'json'
        });
    </script>
</body>