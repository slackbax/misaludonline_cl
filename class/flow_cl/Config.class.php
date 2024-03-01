<?php
$base_url = 'https://www.misaludonline.cl/scheduling/payment';
//$url = FLOW_URL;
$url = FLOW_SANDBOX_URL;
//$api_key = FLOW_API_KEY;
$api_key = FLOW_SANDBOX_API_KEY;
//$secret_key = FLOW_SECRET_KEY;
$secret_key = FLOW_SANDBOX_SECRET_KEY;

$COMMERCE_CONFIG = array(
  "APIKEY" => $api_key,
  "SECRETKEY" => $secret_key,
  "APIURL" => $url,
  "BASEURL" => $base_url
);

class Config
{
  /**
   * @throws Exception
   */
  static function get($name): string
  {
    global $COMMERCE_CONFIG;
    if (!isset($COMMERCE_CONFIG[$name])) {
      throw new Exception("The configuration element thas not exist", 1);
    }
    return $COMMERCE_CONFIG[$name];
  }
}
