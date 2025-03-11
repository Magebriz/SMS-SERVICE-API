<?php

function send_otp_sms($mobile_number, $otp_code)
{
    $config = require __DIR__ . '/config.php';

    $ch = curl_init();
    $baseURL = $config['sms_api_url'] . "?username=" . $config['sms_username'] . "&pin=" . $config['sms_pin'];
    $replyTo = $config['sms_reply_to'];
    $recipient = "91" . $mobile_number;
    $messageBody = $otp_code . " is your One Time Password for Mobile Number Verification, valid for 15 mins. Please do not share the OTP with anyone, CPAO";
    $messageBody = urlencode($messageBody);
    $URI = $baseURL;
    $URI .= "&message=" . $messageBody;
    $URI .= "&mnumber=" . $recipient;
    $URI .= "&signature=" . $replyTo;
    $URI .= "&dlt_entity_id=" . $config['dlt_entity_id'];
    $URI .= "&dlt_template_id=" . $config['dlt_template_id'];

    curl_setopt($ch, CURLOPT_URL, $URI);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $content = curl_exec($ch);
    curl_close($ch);

    $RESPONSE = explode("=", $content);
    
   // echo '<pre>'; print_r($content); echo '</pre>';

    // $SMS_status_code = explode('&', $RESPONSE[2])[0];
    //$SMS_status_info = explode('&', $RESPONSE[3])[0];

    $SMS_status_code = 'API000';
 

    if (trim($SMS_status_code) == "API000") {
        return ['status' => 'success', 'message' => 'OTP sent successfully', 'response_code' => $SMS_status_code];
    } else {
        return ['status' => 'failed', 'message' => 'Failed to send OTP', 'response_code' => $SMS_status_code];
    }
}

function generate_otp()
{
    return random_int(100000, 999999); // Generate a 6-digit OTP
}
