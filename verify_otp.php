<?php
require __DIR__ . '/db.php';

$request = json_decode(file_get_contents('php://input'), true);
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); 
error_reporting('-1');
//print_r($_GET);
header("Content-Type:application/json");

$mobile_number = $request['mobile_number'] ?? null;

$user_id = $request['user_id'] ?? null;

$otp_code = $request['otp_code'] ?? null;

// if (!$mobile_number || !$user_id || !$otp_code) {
//     echo json_encode(['status' => 'error', 'message' => 'Mobile number, PPO number, and OTP code are required']);
//    // exit;
// }

$connection = getOracleConnection();

  $query = "SELECT OTP, EXPIRY_OTP_DATE FROM USER_OTP_TBL where OTP= '".$otp_code."' AND STATUS='0' and
	to_char(EXPIRY_OTP_DATE,'YYYY/MM/DD/HH24/MI/SS') >= to_char(sysdate,'YYYY/MM/DD/HH24/MI/SS') and PPO_NUM = '".$user_id."' and MOBILE_NUMBER= '".$mobile_number."'  ";
	

$statement = oci_parse($connection, $query);
// oci_bind_by_name($statement, ':ppo_num', $user_id);
// oci_bind_by_name($statement, ':mobile_number', $mobile_number);
oci_execute($statement);

$row = oci_fetch_assoc($statement);

//echo '<pre>'; print_r($row); echo '</pre>'; exit;
oci_free_statement($statement);


if ($row) {
    $stored_otp = $row['OTP'];
    $expiry_date = $row['EXPIRY_OTP_DATE'];
    
    if ($stored_otp == $otp_code && strtotime($expiry_date) < time()) {
        echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
    }  else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'OTP has expired / Not Found records']);
}
oci_close($connection);
?>