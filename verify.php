<?php
class Verify
{
    const SMS  = 'sms';
    const USSD = 'ussd';
    const TTS  = 'tts';

    protected $key;
    protected $secret;
    protected $from;
    
    protected $salt = null;
    protected $length = 4;
    
    const API_URI = 'http://rest.nexmo.com/%s/json';
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
    
    public function get($id, $params)
    {
        error_log($id);

        foreach(array('number', 'pin') as $param){
            if(!isset($params[$param])){
                throw new Exception('missing required param: ' . $param, 400);
            }
        }

        //return validation status
        return array('valid' => $this->getHash($params['pin'], $params['number']) == $id);
    }
    
    public function post($params)
    {
        //set some defaults for optional params
        $params = array_merge(array('type' => self::SMS), $params);

        //check for valid request
        if(!isset($params['number'])){
            throw new Exception('missing required param: number', 400);
        }
        
        //generate pin
        $pin = rand(0, pow(10,$this->length)-1);
        $pin = str_pad($pin, $this->length, '0', STR_PAD_LEFT);
        
        //send pin
        $this->sendMessage($params['number'], $pin, $params['type']);
        
        //get hash
        $hash = $this->getHash($pin, $params['number']);
        
        //return hash
        return array('hash' => $hash);
    }
    
    public function run($method, $request, $params)
    {
        switch($method){
            //verify a pin
            case self::HTTP_GET:
                return $this->get($request, $params);
            //get a new token
            case self::HTTP_POST:
                return $this->post($params);               
            default:
                throw new Exception('invalid request: ' . $method, 400);
        }
    }
    
    protected function sendMessage($to, $text, $endpoint = self::SMS)
    {
        //verify valid endpoint
        if(!in_array($endpoint, array(self::SMS, self::TTS, self::USSD))){
            throw new InvalidArgumentException('invalid message type: ' . $endpoint);
        }

        //make the message a little more friendly than just a number
        $text = 'Your PIN is: ' . $text;

        //some channel specific changes
        switch($endpoint){
            case self::TTS:
                $text .= '. ' . $text;
                break;
        }

        $url = sprintf(self::API_URI, $endpoint) . '?' . http_build_query(array(
            'username' => $this->key,
            'password' => $this->secret,
            'from'     => $this->from,
            'to'       => $to,
            'text'     => $text
            ));

        error_log($url);

        $result = file_get_contents($url);
        $result = json_decode($result);

        //channel specific responses
        switch($endpoint){
            case self::USSD:
            case self::SMS:
                foreach($result->messages as $message){
                    if(isset($message->{'error-text'})){
                        throw new RuntimeException($message->{'error-text'}, 500);
                    }
                }
                break;
            case self::TTS:
                if($result->status != 0){
                    throw new RuntimeException('error sending TTS: ' . $result->status);
                }
                break;
        }
    }
    
    protected function getHash($pin, $number)
    {
        return sha1($number . $pin . $this->salt);
    }
}