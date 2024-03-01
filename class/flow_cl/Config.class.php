<?php
$base_url = 'https://www.misaludonline.cl/scheduling/payment';
//$url = 'https://www.flow.cl/api';
$url = 'https://sandbox.flow.cl/api';
//$api_key = '1F07C593-5EA3-4E23-888A-360LDE9726AA';
$api_key = '21FEC410-CA1A-4423-8651-319L11D2B2E5';
//$secret_key = '144ca59a83864d63d178c20e272e1d7181865d3b';
$secret_key = '0f136faa9fd4fb6201e52a6ec599b22d4c3bbf49';

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
