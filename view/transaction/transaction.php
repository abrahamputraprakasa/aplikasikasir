<?php
include('../header.php');
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    die();
}

?>

<div class="container col-lg-6 my-5 center">
    <h3>Transaksi Baru</h3>
    <h4 id="invoiceNumber"></h4>

    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Table Number</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="tableNumber" placeholder="Table Number" autocomplete="off"></input>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Payment Method</label>
        <div class="col-sm-10">
            <select id="paymentMethod" class="form-select" name="category_id">
                <option value="Cash">Cash</option>
                <option value="Debit">Debit</option>
                <option value="Transfer">Transfer</option>
                <option value="QRIS">QRIS</option>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Payment Detail</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="paymentDetail" placeholder="" autocomplete="off"></input>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Notes</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="notesTransaction" placeholder="Notes" autocomplete="off"></input>
        </div>
    </div>

    <button id="buttonAdd" class="btn btn-secondary mt-5">Tambah</button>
    <div class="mt-5" id="divform">
        <form id="formku" enctype="multipart/form-data">
            <div class="mb-3 row" id="divCategory">
                <label class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                    <select id="categories" class="form-select" name="category_id">
                        <option value="0">-- Pilih Kategori --</option>
                    </select>
                    <input type="text" class="form-control" name="category" id="category" placeholder="Category Name" autocomplete="off" disabled></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Item</label>
                <div class="col-sm-10">
                    <select id="items" class="form-select" name="items">
                        <option value="0"></option>
                    </select>
                    <input type="text" class="form-control" name="item" id="item" placeholder="Item Name" autocomplete="off" disabled></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-10">
                    <input type="number" min="1" max="1000" class="form-control" name="quantity" id="quantity" placeholder="Item Quantity" autocomplete="off"></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Notes</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="notes" id="notes" placeholder="Notes" autocomplete="off"></input>
                </div>
            </div>

            <input type="hidden" name="id" id="rowId">
            <button id="buttonCancel" type="button" class="btn btn-secondary">Cancel</button>
            <button id="buttonSubmit" type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>
    <div class="px-0 mt-5">
        <table class="table table-striped-columns" id="tableKu">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Item Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="text-center d-flex flex-column">
            <div>
                <input type="checkbox" id="cbPrintNota" />
                <label for="cbPrintNota">Print Nota</label>
            </div>
            <button id="buttonSubmitTransaction" class="btn btn-success mt-3">Proses Transaksi</button>
        </div>
        <iframe style="display: none" id="myPrintView"></iframe>
    </div>
</div>

<script>
    var currentItems = [];
    var item = null;
    var tableku = $('#tableKu').DataTable({
        paging: false,
        columnDefs: [{
                targets: 3,
                render: function(data) {
                    if (data) {
                        return parseInt(data).toLocaleString('id-ID');
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 5,
                render: function(data) {
                    if (data) {
                        return parseInt(data).toLocaleString('id-ID');
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 7,
                render: function(data) {
                    if (data) {
                        return '<img src="/aplikasi-kasir/' + data + '" style="height: 50px;">';
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 8,
                render: function(data) {
                    return '<button class="btn btn-outline-success btn-sm">Edit</button>' +
                        '<button class="btn btn-outline-danger btn-sm">Remove</button>';
                }
            }
        ]
    });

    tableku.on('click', '.btn-outline-success', function(e) {
        let closestTr = tableku.row(e.target.closest('tr'));
        let dtRow = closestTr.data();
        $("#divform").show();
        $("#buttonAdd").hide();
        $("#buttonSubmit").html('Edit');
        let id = closestTr[0][0];
        $("#rowId").val(id);
        console.log('rowId pas edit', id);
        console.log('dtRow pas edit', dtRow);
        $("#items").hide();
        $("#divCategory").hide();
        $("#item").show();
        $("#notes").val(dtRow[6]);
        $("#item").val(dtRow[2]);
        $("#quantity").val(dtRow[4]);
    });

    tableku.on('click', '.btn-outline-danger', function(e) {
        let data = tableku.row(e.target.closest('tr'));
        tableku.row(data[0][0]).remove().draw();
        resetAllForm();
    });

    $(document).ready(function() {
        getCategories();
        getInvoiceNumber();
        $("#item").hide();
        $("#category").hide();
        $("#categories").on("change", function() {
            var category_id = $(this).val();
            item = null;
            $("#notes").val("");
            $("#quantity").val(1);
            getItems(category_id);
        });
        $("#items").on("change", function() {
            var itemId = $(this).val();
            $("#notes").val("");
            $("#quantity").val(1);
            item = currentItems.find((item) => item.id == itemId);
        });
        $("#divform").hide();
        $("#progressUpload").hide();
    });

    function getCategories() {
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/master/categories.php',
            data: {
                action: 'get',
            },
            success: function(data) {
                data.forEach(element => {
                    $('#categories').append(`<option value="${element.id}">
                        ${element.name}
                    </option>`);
                });
            },
            dataType: 'json'
        });
    }

    function getInvoiceNumber() {
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/transaction/transaction.php',
            data: {
                action: 'getTransactionCountThisMonth',
            },
            success: function(data) {
                let transactionCountThisMonth = data.count;

                let objectDate = new Date();
                let month = objectDate.getMonth();
                if (month < 10) {
                    month = `0${month}`;
                }
                let year = objectDate.getFullYear();
                let transactionCount = '' + (transactionCountThisMonth + 1);
                let invoiceNumber = 'INV' + year + '' + month + '' + transactionCount.padStart(4, '0');
                $("#invoiceNumber").html(invoiceNumber);
            },
            dataType: 'json'
        });
    }

    function getItems(category_id) {
        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/master/items.php',
            data: {
                action: 'get',
                category_id: category_id,
            },
            success: function(data) {
                currentItems = data;
                $('#items').empty()
                $('#items').append('<option value="0">-- Pilih Item --</option>');
                data.forEach(element => {
                    $('#items').append(`<option value="${element.id}">
                        ${element.name}
                    </option>`);
                });
            },
            dataType: 'json'
        });
    }

    function printNota() {
        $("#myPrintView").attr('src', '/aplikasi-kasir/view/transaction/invoice.php');
        $("#myPrintView").on("load", function() {
            let iframe = document.getElementById("myPrintView").contentWindow;
            iframe.focus();
            iframe.print();
        });
    }

    function resetAllForm() {
        $("#divform").hide();
        $("#formku").trigger('reset');
        $("#buttonAdd").show();
        $("#buttonSubmit").html('Tambah');
        $("#items").show();
        $("#divCategory").show();
        $("#item").hide();
        $("#category").hide();
        $("#tableNumber").val("");
    }

    $("#buttonSubmitTransaction").on('click', function() {
        var data = tableku
            .rows()
            .data();

        if (data.length == 0) {
            alert('Mohon isi data terlebih dahulu');
            return;
        }

        let items = [];
        data.each((element) => {
            console.log(element);
            items.push(element);
        });
        const tableNumber = $("#tableNumber").val();
        const notes = $("#notesTransaction").val();
        const paymentMethod = $("#paymentMethod").val();
        const paymentDetail = $("#paymentDetail").val();
        const invoiceNumber = $("#invoiceNumber").html();

        let text = `Apakah anda yakin akan membuat transaksi ini?`;
        if (confirm(text) == true) {
            if (!tableNumber) {
                alert('Mohon isi Table Number terlebih dahulu');
                return;
            }
        } else {
            return;
        }

        const submitData = {
            action: 'insert',
            items: items,
            table_number: tableNumber,
            notes: notes,
            payment_method: paymentMethod,
            payment_details: paymentDetail,
            invoice_number: invoiceNumber,
        };

        const isPrintNotaChecked = $("#cbPrintNota").is(":checked");
        $.ajax({
            type: "POST",
            url: '/aplikasi-kasir/api/transaction/transaction.php',
            data: submitData,
            success: function(data) {
                tableku.clear().draw();
                resetAllForm();
                getInvoiceNumber();
                if (isPrintNotaChecked) {
                    printNota();
                }
            },
            dataType: 'json'
        });
    });

    $("#buttonAdd").on('click', function() {
        $("#divform").show();
        $("#buttonAdd").hide();
        $("#divOldPhoto").hide();
        $("#quantity").val(1);
    });

    $("#buttonCancel").on('click', function() {
        resetAllForm();
    });

    function editRow(rowId, isAdd) {
        var dtRow = tableku.rows(rowId).data()[0];
        if (isAdd) {
            dtRow[4] = parseInt(dtRow[4]) + parseInt($("#quantity").val());
            var subtotal = parseInt(dtRow[4]) * dtRow[3];
            dtRow[5] = subtotal;
        } else {
            dtRow[6] = $("#notes").val();
            dtRow[4] = $("#quantity").val();
            var subtotal = parseInt(dtRow[4]) * dtRow[3];
            dtRow[5] = subtotal;
        }
        tableku.row(rowId).data(dtRow).draw();
        if (!isAdd) {
            resetAllForm();
        }
    }

    $("#formku").submit(function() {
        event.preventDefault();
        if ($("#buttonSubmit").text() === "Edit") {
            var id = $("#rowId").val();
            editRow(id, false);
        } else {
            if (!item) {
                alert('Pilih item terlebih dahulu');
                return;
            }

            var notes = $("#notes").val();

            var isAdd = true;
            tableku.rows().data().each((element, index) => {
                if (element[6] == notes && element[1] == item.id) {
                    editRow(index, true);
                    isAdd = false;
                }
            });

            if (!isAdd) {
                return;
            }

            var quantity = $("#quantity").val();
            var subtotal = parseInt(quantity) * item.price;
            var increment = tableku.rows().data().length;
            var row = [
                increment + 1,
                item.id,
                item.name,
                item.price,
                quantity,
                subtotal,
                notes,
                item.image_url,
                increment,
            ];
            tableku.row.add(row).draw();
        }
    });
</script>

<?php
include('../footer.php');
?>