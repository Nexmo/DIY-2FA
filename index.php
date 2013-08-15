<?php
/**
 * Simple phone verification script. Can be used to verify a user owns a number
 * or for 2nd factor authentication.
 */

require_once 'verify.php';

//Nexmo credentials may be optionally defined elsewhere
defined('NEXMO_KEY')    || (getenv('NEXMO_KEY')    AND define('NEXMO_KEY', getenv('NEXMO_KEY')));
defined('NEXMO_SECRET') || (getenv('NEXMO_SECRET') AND define('NEXMO_SECRET', getenv('NEXMO_SECRET')));
defined('NEXMO_FROM')   || (getenv('NEXMO_FROM')   AND define('NEXMO_FROM', getenv('NEXMO_FROM')));

try{
    //extract request relative to this script
    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $script  = $_SERVER['SCRIPT_NAME'];
    $path    = pathinfo($script);
    $dir     = $path['dirname'];

    //remove directory / script name if fund
    if(0 === strpos($request, $script)){
        $request = substr($request, strlen($script));
    } elseif(0 === strpos($request, $dir)) {
        $request = substr($request, strlen($dir));
    }

    //remove leading /
    $request = ltrim($request, '/');

    $verify = new Verify(NEXMO_KEY, NEXMO_SECRET, NEXMO_FROM);
    $response = $verify->run($_SERVER['REQUEST_METHOD'], $request, array_merge($_GET, $_POST));
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
