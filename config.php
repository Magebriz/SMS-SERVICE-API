<?php
 $IN_CPAO ="(DESCRIPTION =(ADDRESS_LIST =
 (ADDRESS = (PROTOCOL = TCP)(HOST = 10.23.49.94)(PORT = 1521))
)
(CONNECT_DATA =
 (SERVICE_NAME = CPAO)
)
)";

return [
    'sms_api_url' => "http://164.100.14.211/failsafe/HttpLink",
    'sms_username' => 'cpao.sms',
    'sms_pin' => 'Yr%243tK%237bZ',
    'sms_reply_to' => 'CPAOSM',
    'dlt_entity_id' => '1001593660000018216',
    'dlt_template_id' => '1007160775781488354',
    'oracle' => [
        'username' => 'pgmis',
        'password' => 'pgmis123',
        'connection_string' => $IN_CPAO,
    ],
];