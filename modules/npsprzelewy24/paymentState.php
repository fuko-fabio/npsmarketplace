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

$p24_error_code = Tools::getValue('p24_error_code');
$p24_token = Tools::getValue('p24_token'); 
$p24_session_id = Tools::getValue('p24_session_id');
$m = new NpsPrzelewy24();
PrestaShopLogger::addLog('Starting background payment validation. GET params: '.implode(' | ', $_GET).' POST params: '.implode(' | ', $_POST));
if (isset($p24_session_id) && !empty($p24_session_id)) {
    $session_id_array = explode('|', $p24_session_id);
    $id_cart = $session_id_array[1];
    $id_order = Order::getOrderByCartId($id_cart);
    if (empty($p24_error_code)) {
        $validator = new P24PaymentValodator(
            $p24_session_id,
            Tools::getValue('p24_amount'),
            Tools::getValue('p24_currency'),
            Tools::getValue('p24_order_id'),
            Tools::getValue('p24_method'),
            Tools::getValue('p24_statement'),
            Tools::getValue('p24_sign')
        );
        $result = $validator->validate($p24_token, true);
        if ($result['error'] == 0) {
            PrestaShopLogger::addLog('Background payment. Verification success. Session ID: '.$p24_session_id);
            $dispatcher = new P24TransactionDispatcher($id_cart);
            $dispatcher->dispatchMoney();
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
        $m->reportError(array(
            'Background payment. Unabe to verify payment. Error code: '.$p24_error_code,
            'Requested URL: '.Context::getContext()->link->getModuleLink('npsprzelewy24', 'paymentState'),
            'GET params: '.implode(' | ', $_GET),
            'POST params: '.implode(' | ', $_POST),
        ));
    }
} else {
    $m->reportError(array(
        'Background payment. Unabe to verify payment. Missing session ID.',
        'Requested URL: '.Context::getContext()->link->getModuleLink('npsprzelewy24', 'paymentState'),
        'GET params: '.implode(' | ', $_GET),
        'POST params: '.implode(' | ', $_POST),
    ));
}
