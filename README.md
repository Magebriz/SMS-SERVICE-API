# SMS-SERVICE-API

URL: POST sms_service_api/send_otp.php
Request Body:

{
    "mobile_number": "7210764336",
    "user_id": "PPO123456",
    "USER_FROM": "R",
    "U_FLAG": "N"
}

2. Verify OTP

URL: POST sms_service_api/verify_otp.php
Request Body:
{
    "mobile_number": "7210764336",
    "user_id": "PPO123456",
    "otp_code": "450234"
}
