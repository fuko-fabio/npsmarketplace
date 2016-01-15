<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2016 npsoftware
*/
include(dirname(__FILE__).'/../../config/config.inc.php');
include dirname(__FILE__).'/../../init.php';
include_once(_PS_MODULE_DIR_.'npsprzelewy24/npsprzelewy24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');

PrestaShopLogger::addLog('Starting background payment dispatcher. GET params: '.implode(' | ', $_GET).' POST params: '.implode(' | ', $_POST));

$dispatcher = new P24TransactionDispatcher(Tools::getValue('id_cart'));
$dispatcher->dispatchMoney(Tools::getValue('retry'));
