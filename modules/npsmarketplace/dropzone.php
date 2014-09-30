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
$fu->setToken(trim(Tools::getValue('token')));

switch ($method) {
  case 'PUT':
  case 'POST':
    $files = $fu->process();
    header('Content-Type: application/json');
    echo json_encode($files[0]);
    break;
  case 'DELETE':
    $fu->remove(trim(Tools::getValue('name')));
    break;
  default:
    break;
}

?>