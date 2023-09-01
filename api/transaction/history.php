<?php
    require_once('../database.php');

    // echo(json_encode($_GET));
    // return;

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

        // echo json_encode($row);
        // return;

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
//         {
//     "draw": 1,
//     "recordsTotal": 57,
//     "recordsFiltered": 57,
//     "data": [
//         [
//             "Angelica",
//             "Ramos",
//             "System Architect",
//             "London",
//             "9th Oct 09",
//             "$2,875"
//         ],
//         [
//             "Ashton",
//             "Cox",
//             "Technical Author",
//             "San Francisco",
//             "12th Jan 09",
//             "$4,800"
//         ],
//         ...
//     ]
// }
        echo json_encode($datatableFormat);
    }

