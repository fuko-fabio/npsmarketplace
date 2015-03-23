<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../config/config.inc.php');
include dirname(__FILE__).'/../../init.php';
include(_PS_MODULE_DIR_.'npsprzelewy24/npsprzelewy24.php');
include(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');
include(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');

PrestaShopLogger::addLog('Starting background payment validation. GET params: '.implode(' | ', $_GET).' POST params: '.implode(' | ', $_POST));

$m = new NpsPrzelewy24();
$m->validateP24Response(
    Tools::getValue('p24_error_code'),
    Tools::getValue('p24_token'),
    Tools::getValue('p24_session_id'),
    Tools::getValue('p24_amount'),
    Tools::getValue('p24_currency'),
    Tools::getValue('p24_order_id'),
    Tools::getValue('p24_method'),
    Tools::getValue('p24_statement'),
    Tools::getValue('p24_sign')
);