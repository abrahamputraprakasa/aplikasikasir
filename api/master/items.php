<?php
    require_once('../database.php');

    if (isset($_POST['action'])){
        switch ($_POST['action']){
            case 'insert':
                insert($_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['imageUrl'] ?? null);
                break;
            case 'update':
                update($_POST['id'], $_POST['category_id'], $_POST['name'], $_POST['description'], isset($_POST['imageUrl']) ? $_POST['imageUrl'] : null);
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
        $sql = "SELECT i.*,c.name as category_name FROM items i LEFT JOIN categories c ON i.category_id=c.id";
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
        $stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        echo json_encode($result->fetch_assoc());
    }

    function insert($categoryId, $name, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("INSERT INTO items (`category_id`, `name`,`description`,`image_url`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $categoryId, $name, $description, $imageUrl);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function update($id, $categoryId, $name, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("UPDATE items SET `name`=?, `description`=?, `image_url`=?, `category_id`=? WHERE id=?");
        $stmt->bind_param("sssii", $name, $description, $imageUrl, $categoryId, $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function delete($id){
        $conn = createConnection();
        $stmt = $conn->prepare("DELETE FROM items WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }
