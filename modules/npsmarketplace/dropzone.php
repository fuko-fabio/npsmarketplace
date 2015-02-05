<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/classes/FileUploader.php');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

$fu = new FileUploader('file');

switch ($method) {
  case 'PUT':
  case 'POST':
    $files = $fu->process();
    header('Content-Type: application/json');
    if ($files[0]['error']) {
        http_response_code(400);
    }
    die(json_encode($files[0]));
    break;
  case 'DELETE':
    $fu->remove(trim(Tools::getValue('name')));
    break;
  default:
    break;
}

?>