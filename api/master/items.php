<?php
    require_once('../database.php');

    if (isset($_POST['action'])){
        switch ($_POST['action']){
            case 'insert':
                insert($_POST['category_id'], $_POST['name'], $_POST['price'], $_POST['description'], $_POST['imageUrl'] ?? null);
                break;
            case 'update':
                update($_POST['id'], $_POST['category_id'], $_POST['name'], $_POST['price'], $_POST['description'], isset($_POST['imageUrl']) ? $_POST['imageUrl'] : null);
                break;
            case 'delete':
                delete($_POST['id']);
                break;
        }
    }

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get':
                $categoryId = 0;
                if (isset($_GET['category_id'])){
                    $categoryId = $_GET['category_id'];
                }
                get($categoryId);
                break;
            case 'detail':
                detail($_GET['id']);
                break;
        }
    }

    function get($categoryId = 0){
        $conn = createConnection();
        $sql = "SELECT i.*,c.name as category_name FROM items i LEFT JOIN categories c ON i.category_id=c.id";

        if ($categoryId) {
            $sql .= " WHERE c.id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $categoryId);
        } else {
            $stmt = $conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
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

    function insert($categoryId, $name, $price, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("INSERT INTO items (`category_id`, `name`, `price`, `description`,`image_url`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $categoryId, $name, $price, $description, $imageUrl);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function update($id, $categoryId, $name, $price, $description, $imageUrl){
        $conn = createConnection();
        $stmt = $conn->prepare("UPDATE items SET `name`=?, `price`=?, `description`=?, `image_url`=?, `category_id`=? WHERE id=?");
        $stmt->bind_param("sissii", $name, $price, $description, $imageUrl, $categoryId, $id);
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
