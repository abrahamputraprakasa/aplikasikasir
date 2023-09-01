<?php
include('../header.php');
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    die();
}
?>

<div class="container col-lg-6 my-5 center">
    <h2>Barang</h2>

    <button id="buttonAdd" class="btn btn-secondary mt-5">Tambah</button>
    <div class="mt-5" id="divform">
        <form id="formku" enctype="multipart/form-data">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                    <select id="categories" class="form-select" name="category_id">
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" autocomplete="off" required></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="description" id="description" placeholder="Description" autocomplete="off"></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Price</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="price" id="price" placeholder="Price" autocomplete="off" required></input>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Photo</label>
                <div class="col-sm-10">
                    <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
                </div>
                <input type="hidden" name="imageUrl" id="filename">
                <input type="hidden" name="id" id="rowId">
            </div>
            <div class="mb-3 row" id="divOldPhoto">
                <label class="col-sm-2 col-form-label">Old Photo</label>
                <img id="oldPhoto"></img>
            </div>
            <progress id="progressUpload"></progress>
            <button id="buttonCancel" type="button" class="btn btn-secondary">Cancel</button>
            <button id="buttonSubmit" type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>
    <div class="px-0 mt-5">
        <table class="table table-striped-columns" id="tableKu">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Category</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Description</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
    var tableku = $('#tableKu').DataTable({
        ajax: {
            url: '/aplikasi-kasir/api/master/items.php?action=get',
            dataType: 'json',
            dataSrc: ""
        },
        columns: [{
                data: 'id'
            },
            {
                data: 'category_name'
            },
            {
                data: 'name'
            },
            {
                data: 'price'
            },
            {
                data: 'description'
            },
            {
                data: 'image_url'
            },
            {
                data: 'id'
            }
        ],
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
                        return '<img src="/aplikasi-kasir/' + data + '" style="height: 100px;">';
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 6,
                render: function(data, type, row) {
                    if (data) {
                        return '<button onclick="editRow(' + data + ')" class="btn btn-outline-success btn-sm">Edit</button>' +
                            '<button onclick="deleteRow(' + data + ',\'' + row.name + '\')" class="btn btn-outline-danger btn-sm">Delete</button>';
                    } else {
                        return '';
                    }
                }
            }
        ]
    });

    function deleteRow(id, name) {
        let text = `Apakah anda yakin akan menghapus data ${name} ini?`;
        if (confirm(text) == true) {
            $.ajax({
                type: "POST",
                url: '/aplikasi-kasir/api/master/items.php',
                data: {
                    action: 'delete',
                    id: id
                },
                success: function(data) {
                    tableku.ajax.reload();
                },
                dataType: 'json'
            });
        }
    }

    $(document).ready(function() {
        tableku.ajax.reload();
        getCategories();
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

    function editRow(id) {
        $("#divform").show();
        $("#buttonAdd").hide();
        $("#buttonSubmit").html('Edit');
        $("#rowId").val(id);
        $("#divOldPhoto").show();

        $.ajax({
            type: "GET",
            url: '/aplikasi-kasir/api/master/items.php',
            data: {
                action: 'detail',
                id: id
            },
            success: function(data) {
                $("#name").val(data.name);
                $("#description").val(data.description);
                $("#price").val(data.price);
                $("#categories").val(data.category_id);
                if (data.image_url) {
                    $("#filename").val(data.image_url);
                    $("#oldPhoto").attr('src', '/aplikasi-kasir/' + data.image_url);
                } else {
                    $("#oldPhoto").attr('src', '');
                }
            },
            dataType: 'json'
        });
    }

    $("#buttonAdd").on('click', function() {
        $("#divform").show();
        $("#buttonAdd").hide();
        $("#divOldPhoto").hide();
    });

    $("#buttonCancel").on('click', function() {
        $("#divform").hide();
        $("#formku").trigger('reset');
        $("#buttonAdd").show();
        $("#buttonSubmit").html('Tambah');
    });

    $("#formku").submit(function() {
        event.preventDefault();
        let data = $(this).serialize();
        var action = 'insert';

        if ($("#buttonSubmit").text() === "Edit") {
            action = 'update';
        }
        $.ajax({
            type: "POST",
            url: '/aplikasi-kasir/api/master/items.php',
            data: data + '&action=' + action,
            success: function(data) {
                tableku.ajax.reload();
                $("#divform").hide();
                $("#buttonAdd").show();
                $("#buttonSubmit").html('Tambah');
                $("#formku").trigger('reset');
            },
            dataType: 'json'
        });
    });

    $(':file').on('change', function() {
        var file = this.files[0];
        if (!file) {
            return;
        }
        $("#progressUpload").show();
        $.ajax({
            // Your server script to process the upload
            url: '/aplikasi-kasir/api/upload.php',
            type: 'POST',

            // Form data
            data: new FormData($('form')[0]),

            // Tell jQuery not to process data or worry about content-type
            // You *must* include these options!
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            // Custom XMLHttpRequest
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            $('progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });
                        }
                    }, false);
                }
                return myXhr;
            },

            success: function(data) {
                console.log(data);
                if (data.filename) {
                    $("#filename").val(data.filename);
                } else {
                    alert(data.message);
                }
                $("#progressUpload").hide();
            },
        });
    });
</script>

<?php
include('../footer.php');
?>