<?php
class Verify
{
    protected $key;
    protected $secret;
    protected $from;
    
    protected $salt = null;
    protected $length = 4;
    
    const API_URI = 'http://rest.nexmo.com/sms/json?username=%1$s&password=%2$s&from=%3$s&to=%4$s&text=%5$s';
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    
    public function __construct($key, $secret, $from, $options = array())
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->from = $from;
        
        $this->salt = $key . $secret;
        
        foreach($options as $option => $value){
            if(property_exists($this, $option)){
                $this->$option = $value;
            }
        }
    }
    
    public function run($method, $params)
    {
        switch($method){
            //verify a pin
            case self::HTTP_GET:
                foreach(array('number', 'pin', 'hash') as $param){
                    if(!isset($params[$param])){
                        throw new Exception('missing required param: ' . $param, 400);
                    }
                }
                
                //return validation status
                return array('valid' => $this->getHash($params['pin'], $params['number']) == $params['hash']);
            //get a new token
            case self::HTTP_POST:
                //check for valid request
                if(!isset($params['number'])){
                    throw new Exception('missing required param: number', 400);
                }
                
                $number = $params['number'];
                
                //generate pin
                $pin = rand(0, pow(10,$this->length)-1);
                $pin = str_pad($pin, $this->length, '0', STR_PAD_LEFT);
                
                //send pin
                $this->sendSms($number, $pin);
                
                //get hash
                $hash = $this->getHash($pin, $number);
                
                //return hash
                return array('hash' => $hash);
            default:
                throw new Exception('invalid request: ' . $method, 400);
        }
    }
    
    protected function sendSms($to, $text)
    {
        $uri = sprintf(self::API_URI, $this->key, $this->secret, $this->from, $to, $text);
        $result = file_get_contents($uri);
        $result = json_decode($result);
        foreach($result->messages as $message){
            if(isset($message->{'error-text'})){
                throw new Exception($message->{'error-text'}, 500);
            }
        }
    }
    
    protected function getHash($pin, $number)
    {
        return sha1($number . $pin . $this->salt);
    }
}