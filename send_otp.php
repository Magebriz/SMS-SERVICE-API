<?php
require __DIR__ . '/db.php';
require __DIR__ . '/otp_helper.php';

$request = json_decode(file_get_contents('php://input'), true);

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); 
error_reporting('-1');

header("Content-Type:application/json");


 $mobile_number = $request['mobile_number'] ?? null;
 $user_id = $request['user_id'] ?? null;
 $user_from = $request['USER_FROM'] ?? 'F';
 $u_flag = $request['U_FLAG'] ?? 'W';
//echo '</br>';
// if (!$mobile_number || !$user_id || $user_from == 'F' || $u_flag == 'W') {
//     echo json_encode(['status' => 'error', 'message' => 'Mobile number and USER ID and uflag are required']);
//     exit;
// }

$otp_code = generate_otp();
$startTime = date("Y-m-d H:i:s");
$otp_expire = date("Y/m/d H:i", strtotime('+15 minutes')); // Properly formatted

// Send OTP via SMS
$sms_response = send_otp_sms($mobile_number, $otp_code);

if ($sms_response['status'] == 'success' || $sms_response['status'] == 'failed') {
    // Insert OTP into the database
    $connection = getOracleConnection();

    // $insert_query=  "INSERT INTO USER_OTP_TBL (PPO_NUM,MOBILE_NUMBER,OTP,STATUS,OTP_DATE,EXPIRY_OTP_DATE,USER_IP,SMS_SEND_STATUS,
	// 		SMS_SEND_DATE,SMS_RESPONSE,U_FLAG,USER_FROM,USER_ID)
	// 	VALUES (:ppo_num, :mobile_number, :otp_code,'0',sysdate,sysdate + interval '5' minute,
	// :REMOTE_ADDR, :sms_status, to_date(:otp_expire,'YYYY/MM/DD/HH24/MI'), :SMS_RESPONSE,'W','F',:ppo_num)";

 $insert_query=  "INSERT INTO USER_OTP_TBL (PPO_NUM,MOBILE_NUMBER,OTP,STATUS,OTP_DATE,EXPIRY_OTP_DATE,USER_IP,SMS_SEND_STATUS,
			SMS_SEND_DATE,SMS_RESPONSE,U_FLAG,USER_FROM,USER_ID)
    VALUES ('$user_id', '$mobile_number', '$otp_code','0',sysdate,sysdate + interval '15' minute, '".$_SERVER['REMOTE_ADDR']."' ,'".$sms_response['status']."',
   to_date('$otp_expire','YYYY/MM/DD/HH24/MI'), '".$sms_response['response_code']."','W','F', '$user_id')";

    // $q = "SELECT mobile_number,USER_IP,PPO_NUM,USER_ID FROM USER_OTP_TBL where PPO_NUM= '".$user_id."' ";
	$result = oci_parse($connection,$insert_query);	
    oci_execute($result, OCI_DEFAULT);
    oci_commit($connection);

	// $dataResult = oci_fetch_all($result, $results);
    // $dbarray = $results;
    // echo "<pre>"; print_r($dbarray); echo "</pre>";

    // $statement = oci_parse($connection, $insert_query);

    //  oci_bind_by_name($statement, ':ppo_num', $user_id);
    //  oci_bind_by_name($statement, ':mobile_number', $mobile_number);
    //  oci_bind_by_name($statement, ':otp_code', $otp_code);
    //  oci_bind_by_name($statement, ':otp_expire', $otp_expire); // Bind the formatted date
    //  oci_bind_by_name($statement, ':REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
    //  oci_bind_by_name($statement, ':sms_status', $sms_response['status']);
    //  oci_bind_by_name($statement, ':SMS_RESPONSE',$sms_response['response_code']);

   // oci_execute($statement, OCI_DEFAULT);
    //oci_commit($connection);
   // oci_free_statement($statement);
    
    if (!$result) {
        $e = oci_error($result);
        echo "Error in query execution: " . $e['message'];
    } else {
        echo "Data inserted successfully.";
    }
  oci_close($connection);

    echo json_encode(['status' => 'success', 'OTP' => $otp_code , 'message' => 'OTP sent and saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP']);
}