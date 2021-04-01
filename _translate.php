<?php
class _translate {

private $_client_id;
private $_client_secret; 
private $grant_type = 'client_credentials';
private $scope_url  = 'http://api.microsofttranslator.com';


public function __construct($clientID, $clientSecret) {

    $this->_client_id       = $clientID;
    $this->_client_secret   = $clientSecret;

}
//Create function for curl 
public function getResponse($url) {

$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL                     => $url,
    CURLOPT_HTTPHEADER              => array(

    'Authorization: Bearer '.$this->getToken(),
    'Content-Type: text/xml'

    ),
    CURLOPT_RETURNTRANSFER          => true,
    CURLOPT_SSL_VERIFYPEER          => false
));

$response = curl_exec($ch);

curl_close($ch);

return $response;
}

//Function to receive access token for translation
public function getToken($clientID, $clientSecret) {

//Set user's id and ST 
$clientID           = $this->_client_id;
$clientSecret           = $this->_client_secret;


$ch = curl_init();
    //Set params for request
    $params = array(
        'grant_type'        => $this->grant_type,
        'scope'             => $this->scope_url,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
    );
    $row = http_build_query($params, '', '&');

curl_setopt_array($ch, array(
        CURLOPT_URL                     =>'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/',
        CURLOPT_POST                    => true,
        CURLOPT_POSTFIELDS              => $row,
        CURLOPT_RETURNTRANSFER          => true,
        CURLOPT_SSL_VERIFYPEER          => false
));


$response = curl_exec($ch);
curl_close($ch);


$response_obj = json_decode($response);

//Receive access token for further operations
return $response_obj->access_token;
}

//Set function for translation
public function getTranslation($fromLanguage, $toLanguage, $text) 
    {
        //Create answer to server with specified curl
        $response = $this->getResponse($this->getURL($fromLanguage, $toLanguage, $text));

        //To delete xml tegs
        return strip_tags($response);
    }

//This function sets url which our bot will call
public function getURL($fromLanguage, $toLanguage, $text) 
{
    return 'http://api.microsofttranslator.com/v2/Http.svc/Translate?text='.urlencode($text).'&to='.$toLanguage.'&from='.$fromLanguage;
}
}


//Default
//Translate user's message
if($message)
{
    //connect to our app with it's id and 
    $translate = new _translate('id', 'secret');

    /* 
    *  Set translation language first - from, second - to.
    *  $message - user's message
    */

    $translation = $translate->getTranslation('en', 'ru', $message);

    //get translation of user's message and send it back to user
    $answer = $translation;

    sendMessage($chat_id, $answer);

}