<?php

try {
    header('Content-type: application/json');
    $con = new PDO('mysql:host=localhost;dbname=csi_v3', 'csiv3', 'CSI_2020');

    $stmt = $con->query('SELECT id, rfid FROM rfid_checkers');
    $machine = $stmt->fetch();
    echo(json_encode($machine));
} catch (PDOException $ex) {
    http_response_code(500);
    echo "Something wrong" . $ex->getMessage();
}
