<?php
/**
 * Simple phone verification script. Can be used to verify a user owns a number
 * or for 2nd factor authentication.
 */

require_once 'verify.php';

//Nexmo credentials may be optionally defined elsewhere
defined('NEXMO_KEY') || define('NEXMO_KEY', 'your_key');
defined('NEXMO_SECRET') || define('NEXMO_SECRET', 'your_secret');
defined('NEXMO_FROM') || define('NEXMO_FROM', 'your_number');

$verify = new Verify(NEXMO_KEY, NEXMO_SECRET, NEXMO_FROM);

try{
    $response = $verify->run($_SERVER['REQUEST_METHOD'], array_merge($_GET, $_POST));
    header('HTTP/1.1 200 OK');
    echo json_encode($response);
} catch (Exception $e) {
    $code = 500;
    if($e->getCode() > 100 AND $e->getCode() < 600){
        $code = $e->getCode();
    }
    header('HTTP/1.1 ' . $code);
    echo json_encode($e->getMessage());
}
