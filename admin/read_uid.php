<?php
header('Content-Type: application/json');
require_once "../connection.php";

function readCardUID() {
    // Adjust this command according to your NFC reading tool
    $uid = shell_exec('nfc-list | grep UID | awk \'{print $3}\'');
    return trim($uid);
}

echo json_encode(['uid' => readCardUID()]);
?>
