<?php

/**
 * Created by michalz on 13.03.14
 */
class NpsPrzelewy24PaymentConfirmationModuleFrontController extends ModuleFrontController {
	public $display_column_left = false;
	public $display_column_right = false;

	public function initContent() {
		$this->display_column_left = false;
		$this->display_column_right = false;
		parent::initContent();

		global $smarty;

		if(isset($_GET['order_id'])){
			$cart = Cart::getCartByOrderId($_GET['order_id']);
			if($cart == null){
				die();
			}

		} else {
			global $cart;
		}

		$address = new Address((int)$cart->id_address_invoice);
		$customer = new Customer((int)($cart->id_customer));

		$amount = $cart->getOrderTotal(true, Cart::BOTH);

		$przelewy24 = new NpsPrzelewy24();
		$currencies = $przelewy24->getCurrency(intval($cart->id_currency));
		$currency = $currencies[0];

		if (isset($currency['decimals']) && $currency['decimals'] == '0') {
			if (Configuration::get('PS_PRICE_ROUND_MODE') != null) {
				switch (Configuration::get('PS_PRICE_ROUND_MODE')) {
					case 0:
						$amount = ceil($amount);
						break;
					case 1:
						$amount = floor($amount);
						break;
					case 2:
						$amount = round($amount);
						break;
				}
			}
		}

		$amount = number_format($amount, 2, '.', '') * 100;

		$s_sid = md5(time());
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'przelewy24_amount` '.'(`s_sid`,`i_id_order`,`i_amount`) '.'VALUES("'.$s_sid.'",'.$cart->id.','.$amount.')');
		$s_lang = new Country((int)($address->id_country));

		$order = Order::getOrderByCartId($cart->id);
		if($order == null){
			$s_descr = '';
			$validationRequired = true;
		} else {
			$s_descr = 'Zamówienie: '.$order;
			$validationRequired = false;
		}


		$url = 'secure.przelewy24.pl';
		if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
    		$url = 'sandbox.przelewy24.pl';
		}

		$smarty->assign(array(
			'productsNumber' => $cart->nbProducts(),
			'ps_version' => _PS_VERSION_,
			'p24_url' => $url,
			'p24_session_id' => $cart->id.'|'.$s_sid, //$sid,
			'p24_id_sprzedawcy' => Configuration::get('NPS_P24_MERCHANT_ID'),
			'p24_kwota' => $amount,
			'p24_opis' => $s_descr,
			'p24_klient' => $customer->firstname.' '.$customer->lastname,
			'p24_adres' => $address->address1." ".$address->address2,
			'p24_kod' => $address->postcode,
			'p24_miasto' => $address->city,
			'p24_language' => strtolower($s_lang->iso_code),
			'p24_kraj' => $s_lang->iso_code,
			'p24_email' => $customer->email,
			'p24_metoda' => Tools::getValue('payment_method'),
			'p24_return_url_ok' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentSuccessful'),
			'p24_return_url_error' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentFailed'),
			'p24_validationRequired' => $validationRequired
		));

		$this->setTemplate('paymentConfirmation.tpl');
	}
}