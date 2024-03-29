<?php
    require_once('../database.php');

    if (isset($_POST['action'])){
        switch ($_POST['action']){
            case 'insert':
                insert($_POST['name'], $_POST['description'], $_POST['imageUrl'] ?? null);
                break;
            case 'update':
                update($_POST['id'], $_POST['name'], $_POST['description'], isset($_POST['imageUrl']) ? $_POST['imageUrl'] : null);
                break;
            case 'delete':
                delete($_POST['id']);
                break;
        }
    }

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get':
                get();
                break;
            case 'detail':
                detail($_GET['id']);
                break;
        }
    }

    function get(){
        $sql = "SELECT * FROM categories";
        $conn = createConnection();
        $result = $conn->query($sql);
        $datas = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        $conn->close();
        echo json_encode($datas);
    }

    function detail($id){
        $conn = createConnection();
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        echo json_encode($result->fetch_assoc());
    }

    function insert($name, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("INSERT INTO categories (`name`,`description`,`image_url`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $imageUrl);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function update($id, $name, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("UPDATE categories SET `name`=?, `description`=?, `image_url`=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $description, $imageUrl, $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function delete($id){
        $conn = createConnection();
        $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }
