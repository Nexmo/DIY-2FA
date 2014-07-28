<?php
//Nexmo credentials may be optionally defined elsewhere

if(file_exists(__DIR__ . '/.local.php')){
    require_once __DIR__ . '/.local.php';
}

defined('NEXMO_KEY')    || (getenv('NEXMO_KEY')    AND define('NEXMO_KEY', getenv('NEXMO_KEY')));
defined('NEXMO_SECRET') || (getenv('NEXMO_SECRET') AND define('NEXMO_SECRET', getenv('NEXMO_SECRET')));
defined('NEXMO_FROM')   || (getenv('NEXMO_FROM')   AND define('NEXMO_FROM', getenv('NEXMO_FROM')));