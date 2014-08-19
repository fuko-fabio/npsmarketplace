<?php
/**
 * Created by michalz on 14.03.14
 */
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
include(dirname(__FILE__).'/../npsprzelewy24.php');

if ($_POST['p24_session_id'] != null) {
	global $cart;

	$przelewy24 = new NpsPrzelewy24();
	$sessionID = explode('|', $_POST["p24_session_id"]);
	$sessionID = preg_replace('/[^a-z0-9]/i', '', $sessionID[1]);

	$orderPrzelewy24 = Db::getInstance()->getRow('SELECT `i_id_order`,`i_amount` FROM `'._DB_PREFIX_.'przelewy24_amount` WHERE `s_sid`="'.$sessionID.'"');
	$amountGrosh = $orderPrzelewy24['i_amount']; // w groszach!

	$cartID = $orderPrzelewy24['i_id_order'];

	$orderBeginingState = Configuration::get('P24_ORDER_STATE_1');

	$customer = new Customer((int)($cart->id_customer));

	$result = $przelewy24->validateOrder((int)$cartID, (int)$orderBeginingState, $amountGrosh, 'przelewy24.pl', NULL, array(), NULL, false, $customer->secure_key);

	$orderID = Order::getOrderByCartId(intval($cartID));

	exit('OK'.$orderID);
} else {
	exit('INVALID_SESSION_ID');
}