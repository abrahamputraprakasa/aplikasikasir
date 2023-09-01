<?php
    require_once('../database.php');
    get($_GET);

    function get($params){
        $limit = $params['length'];
        $offset = $params['start'];
        $search = $params['search']['value'];
        $originalQuery = "SELECT t.*,u.name as user_name FROM transactions t LEFT JOIN users u ON u.id=t.user_id";
        $countQuery = "SELECT count(*) as count FROM transactions t LEFT JOIN users u ON u.id=t.user_id";

        $orderBy = 'id';
        $orderDir = 'desc';
        if (isset($params['order'][0]['column']) && $params['order'][0]['column']){
            $orderBy = $params['order'][0]['column'];
            $orderDir = $params['order'][0]['dir'];
        }
        $sql = "$originalQuery WHERE invoice_number LIKE '%$search%' OR u.name LIKE '%$search%' ORDER BY $orderBy $orderDir LIMIT $limit OFFSET $offset";
        $conn = createConnection();
        $result = $conn->query($sql);
        $datas = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        $conn->close();

        $sql = "$countQuery";
        $conn = createConnection();
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();

        $countFiltered = (int) $row['count'];
        if ($search){
            $countFiltered = count($datas);
        }
        $datatableFormat = [
            "draw" => (int) $params['draw'],
            "recordsTotal" => (int) $row['count'],
            "recordsFiltered" => $countFiltered,
            "data" => $datas
        ];
        echo json_encode($datatableFormat);
    }

