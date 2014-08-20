<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
include(dirname(__FILE__).'/../przelewy24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');

error_log(implode(" | ", $_POST));
$p24_merchant_id = Tools::getValue('p24_merchant_id');
$p24_pos_id = Tools::getValue('p24_pos_id');
$p24_session_id = Tools::getValue('p24_session_id');
$p24_amount = Tools::getValue('p24_amount');
$p24_currency = Tools::getValue('p24_currency');
$p24_order_id = Tools::getValue('p24_order_id');
$p24_method = Tools::getValue('p24_method');
$p24_statement = Tools::getValue('p24_statement');
$p24_sign = Tools::getValue('p24_sign');

if (isset($p24_merchant_id)) {
    $session_id_array = explode('|', $p24_session_id);
    $id_cart = $session_id_array[1];
    $timestamp = $session_id_array[1];
    $p24_payment = P24Payment::getBySessionId($p24_session_id);

    if($p24_payment->id != null
        && $p24_payment->session_id == $p24_session_id
        && $p24_payment->amount == $p24_amount
        && $p24_payment->currency_iso == $p24_currency
        && $p24_payment->id_cart == $id_cart
        && $p24_payment->timestamp == $timestamp) {

        $paymen_statement = new P24PaymentStatement();
        $paymen_statement->id_payment = $p24_payment->id;
        $paymen_statement->order_id = $p24_order_id;
        $paymen_statement->payment_method = $p24_method;
        $paymen_statement->statement = $p24_statement;
        $paymen_statement->save();
        d('Payment state success: '.$_POST);
     } else {
        d('Invalid state: '.$_POST.' object: '.$p24_payment);
     }
} else {
    d('No session id: '.$_POST);
}
