<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../config/config.inc.php');
include dirname(__FILE__).'/../../init.php';
include(dirname(__FILE__).'/npsprzelewy24.php');
include(dirname(__FILE__).'/classes/P24PaymentValidator.php');

$p24_error_code = Tools::getValue('p24_error_code');
$p24_token = Tools::getValue('p24-token'); 
$session_id_array = explode('|', Tools::getValue('p24_session_id'));
$id_cart = $session_id_array[1];
$id_order = Order::getOrderByCartId($id_cart);
$m = new NpsPrzelewy24();
if (empty($p24_error_code)) {
    $validator = new P24PaymentValodator(
        Tools::getValue('p24_session_id'),
        Tools::getValue('p24_amount'),
        Tools::getValue('p24_currency'),
        Tools::getValue('p24_order_id'),
        Tools::getValue('p24_method'),
        Tools::getValue('p24_statement'),
        Tools::getValue('p24_sign')
    );
    $result = $validator->validate($p24_token);
    if ($result['error'] == 0) {
        PrestaShopLogger::addLog('PaymentState: Payment verified. Session ID: '.Tools::getValue('p24_session_id'));
    } else {
        $history = new OrderHistory();
        $history->id_order = intval($order_id);
        $history->changeIdOrderState(8, intval($order_id));
        $history->addWithemail(true);
    }
} else {
    $history = new OrderHistory();
    $history->id_order = intval($order_id);
    $history->changeIdOrderState(8, intval($order_id));
    $history->addWithemail(true);
    PrestaShopLogger::addLog('PaymentState: Unabe to verify payment. Error code: '.$p24_error_code);
    $m->reportError(array(
        'Requested URL: '.$this->context->link->getModuleLink('npsprzelewy24', 'paymentState'),
        'GET params: '.implode(' | ', $_GET),
        'POST params: '.implode(' | ', $_POST),
    ));
}