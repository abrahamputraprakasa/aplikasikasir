<?php
    require_once('../database.php');

    $id = $_GET['id'] ?? 0;
    if (!$id){
        $id = getLastTransactionId()['id'];
    }
    $transaction = detail($id);
    $transactionDetail = detailTransaction($id);
    $response = [
        'transaction' => $transaction,
        'transaction_detail' => $transactionDetail
    ];
    echo json_encode($response);

    function detailTransaction($id){
        $conn = createConnection();
        $stmt = $conn->prepare("SELECT td.*,i.name as item_name,i.price as item_price FROM transaction_details td LEFT JOIN items i ON i.id=td.item_id WHERE transaction_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $datas = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        $conn->close();
        return $datas;
    }

    function detail($id){
        $conn = createConnection();
        $stmt = $conn->prepare("SELECT t.*,u.name as user_name FROM transactions t LEFT JOIN users u ON u.id=t.user_id WHERE t.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        return $result->fetch_assoc();
    }

    function getLastTransactionId(){
        $conn = createConnection();
        $stmt = $conn->prepare("SELECT id FROM transactions ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        return $result->fetch_assoc();
    }

