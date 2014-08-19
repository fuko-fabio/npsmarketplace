<?php

/**
 * Created by michalz on 19.03.14
 */

require_once(dirname(_PS_MODULE_DIR_).'/modules/przelewy24/validation_lib.php');

class NpsPrzelewy24PaymentSuccessfulModuleFrontController extends ModuleFrontController {
	public $display_column_left = false;
	public $display_column_right = false;

	public function initContent(){
		global $smarty, $cart;
		$this->display_column_left = false;
		$this->display_column_right = false;
		parent::initContent();

		if (!empty($_POST)) {
			$b_is_SSL = function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close');

			$p24_session_id = $_POST["p24_session_id"];
			$p24_order_id = $_POST["p24_order_id"];
			$p24_id_sprzedawcy = Configuration::get('NPS_P24_MERCHANT_ID');
			//TWÓJ ID_SPRZEDAWCY;
			$sa_sid = explode('|', $_POST["p24_session_id"]);
			$sa_sid = preg_replace('/[^a-z0-9]/i', '', $sa_sid[1]);
			$o_order = Db::getInstance()->getRow('SELECT `i_id_order`,`i_amount` FROM `'._DB_PREFIX_.'przelewy24_amount` WHERE `s_sid`="'.$sa_sid.'"');
			$p24_kwota = (int)$o_order['i_amount'];
			// WYNIK POBRANY Z TWOJEJ BAZY (w groszach)
			$i_id_order = $o_order['i_id_order'];

			$WYNIK = false;
			$url = 'secure.przelewy24.pl';
			if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
				$url = 'sandbox.przelewy24.pl';
			}

			if ($b_is_SSL) {
				$WYNIK = p24_weryfikujSSL($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota, $url);
			} else {
				$WYNIK = p24_weryfikujNoSSL($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota, $url);
			}
			$o_przelewy24 = new NpsPrzelewy24();
			$opis = "przelewy24.pl";

			$cart = new Cart($i_id_order);
			$secure_key = $cart->secure_key;

			if ($WYNIK[0] == 'TRUE') {

				$order_id = Order::getOrderByCartId(intval($i_id_order));
				$order = new Order($order_id);

				$history = new OrderHistory();

				$history->id_order = intval($order_id);

				$order_state = Configuration::get('NPS_P24_ORDER_STATE_2');
				$history->changeIdOrderState($order_state, intval($order_id));
				$history->addWithemail(true);

				$payments = $order->getOrderPaymentCollection();
				if (count($payments) > 0) {
					$payments[0]->transaction_id = $p24_order_id;
					$payments[0]->update();
				}
			}
			$smarty->assign('p24_status', 'success');
		}

		$this->setTemplate('paymentSuccessful.tpl');
	}
}