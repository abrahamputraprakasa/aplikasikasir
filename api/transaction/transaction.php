<?php
    require_once('../database.php');

    session_start();
    if (isset($_POST['action'])){
        switch ($_POST['action']){
            case 'insert':
                $items = $_POST['items'];
                $totalPrice = 0;
                $totalItems = 0;
                foreach ($items as $item) {
                    $totalPrice += $item[5];
                    $totalItems++;
                }
                $lastTransactionId = insert($_SESSION['user_id'], $_POST['table_number'], $_POST['notes'], $_POST['payment_method'], $_POST['payment_details'], $totalPrice, $totalItems, $_POST['invoice_number']);
                $successDetail = [];
                foreach ($items as $item) {
                    $insertDetail = insertDetail($lastTransactionId, $item[1], $item[4], $item[6]);
                    $successDetail[] = $insertDetail;
                }

                if ($lastTransactionId){
                    $result = [
                        'transaction' => 1,
                        'transaction_details' => $successDetail
                    ];
                    echo json_encode($result);
                } else {
                    echo 0;
                }
                break;
            case 'update':
                update($_POST['id'], $_POST['user_id'], $_POST['table_number'], $_POST['notes'], $_POST['payment_method'], $_POST['payment_details']);
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
            case 'getTransactionCountThisMonth':
                getTransactionCountThisMonth();
                break;
        }
    }

    function get(){
        $sql = "SELECT * FROM transactions";
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
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        echo json_encode($result->fetch_assoc());
    }

    function getTransactionCountThisMonth(){
        $conn = createConnection();
        $stmt = $conn->prepare("SELECT count(*) as count FROM transactions WHERE YEAR(created_at)=YEAR(now()) AND MONTH(created_at)=MONTH(now())");
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();
        echo json_encode($result->fetch_assoc());
    }

    function insert($user_id, $table_number, $notes, $payment_method, $payment_details, $total_price, $total_item, $invoiceNumber){
        $conn = createConnection();
        $stmt = $conn->prepare("INSERT INTO transactions (`user_id`,`table_number`,`notes`,`payment_method`,`payment_details`,`total_price`,`total_item`,`invoice_number`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssiis",$user_id, $table_number, $notes, $payment_method, $payment_details, $total_price, $total_item, $invoiceNumber);
        $result = $stmt->execute();
        $last_id = $conn->insert_id;
        $conn->close();
        return $last_id;
    }

    function insertDetail($transaction_id, $item_id, $quantity, $notes){
        $conn = createConnection();
        $stmt = $conn->prepare("INSERT INTO transaction_details (`transaction_id`,`item_id`,`quantity`, `notes`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $transaction_id, $item_id, $quantity, $notes);
        $result = $stmt->execute();
        $conn->close();
        return $result;
    }

    function update($id, $user_id, $table_number, $notes, $payment_method, $payment_details, $total_price, $total_item){
        $conn = createConnection();
        $stmt = $conn->prepare("UPDATE transactions SET `user_id`=?,`table_number`=?,`notes`=?,`payment_method`=?,`payment_details`=?,`total_price`=?,`total_item`=?,`invoice_number`=? WHERE id=?");
        $stmt->bind_param("issssiisi", $user_id, $table_number, $notes, $payment_method, $payment_details, $total_price, $total_item, $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function updateDetail($transaction_id, $item_id, $quantity, $notes)
    {
        $conn = createConnection();
        $stmt = $conn->prepare("UPDATE transaction_details SET `transaction_id`=?,`item_id`=?,`quantity`=?, `notes`=? WHERE id=?");
        $stmt->bind_param("iiisi", $transaction_id, $item_id, $quantity, $notes);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }

    function delete($id){
        $conn = createConnection();
        $stmt = $conn->prepare("DELETE FROM transactions WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $conn->close();
        echo $result;
    }
