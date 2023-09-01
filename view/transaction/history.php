<?php
include('../header.php');
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    die();
}
?>

<div class="container col-lg-6 my-5 center">
    <h2>Riwayat Transaksi</h2>

    <div class="px-0 mt-5">
        <table class="table table-striped-columns" id="tableKu">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Kasir</th>
                    <th scope="col">Table</th>
                    <th scope="col">Total Item</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Datetime</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <iframe style="display: none" id="myPrintView"></iframe>
</div>

<script>
    var tableku = $('#tableKu').DataTable({
        ajax: {
            url: '/aplikasi-kasir/api/transaction/history.php'
        },
        processing: true,
        serverSide: true,
        paging: true,
        pagingType: "full_numbers",
        columns: [{
                data: 'id'
            },
            {
                data: 'user_name'
            },
            {
                data: 'table_number'
            },
            {
                data: 'total_item'
            },
            {
                data: 'total_price'
            },
            {
                data: 'invoice_number'
            },
            {
                data: 'payment_method'
            },
            {
                data: 'created_at'
            },
            {
                data: 'id'
            }
        ],
        columnDefs: [{
                targets: 1,
                orderable: false
            },
            {
                targets: 3,
                className: 'dt-body-right',
            },
            {
                targets: 4,
                className: 'dt-body-right',
                render: function(data) {
                    if (data) {
                        return parseInt(data).toLocaleString('id-ID');
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 6,
                render: function(data, type, row) {
                    if (data) {
                        let rendered = data;
                        if (row.payment_details) {
                            rendered += "<br>" + row.payment_details;
                        }
                        return rendered;
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 8,
                orderable: false,
                render: function(data, type, row) {
                    if (data) {
                        return '<button onclick="printNota(' + data + ')" class="btn btn-outline-success btn-sm">Print Nota</button>';
                    } else {
                        return '';
                    }
                }
            }
        ]
    });

    function printNota(id) {
        $("#myPrintView").attr('src', '/aplikasi-kasir/view/transaction/invoice.php?id=' + id);
        $("#myPrintView").on("load", function() {
            let iframe = document.getElementById("myPrintView").contentWindow;
            iframe.focus();
            iframe.print();
        });
    }

    $(document).ready(function() {
        $("#divform").hide();
    });
</script>

<?php
include('../footer.php');
?>