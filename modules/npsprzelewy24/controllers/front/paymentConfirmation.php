<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');

class NpsPrzelewy24PaymentConfirmationModuleFrontController extends ModuleFrontController {

    private $payment_url;

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        $this->context->smarty->assign(array(
            'payment_url' => $this->payment_url
        ));
        $this->setTemplate('payment_confirmation.tpl');
    }

    public function init() {
        parent::init();
        $m = new NpsPrzelewy24();
        if(isset($_GET['order_id'])) {
            $cart = Cart::getCartByOrderId($_GET['order_id']);
            if($cart == null) {
                $this -> errors[] = sprintf($m->l('Requested order with id %s not exists. Please try to contact the customer support'), $_GET['order_id']);
                return;
            }
        } else {
            global $cart;
        }

        $address = new Address((int)$cart->id_address_invoice);
        $customer = new Customer((int)($cart->id_customer));
        $amount = $cart->getOrderTotal(true, Cart::BOTH);
        $npsprzelewy24 = new NpsPrzelewy24();
        $currencies = $npsprzelewy24->getCurrency(intval($cart->id_currency));
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
        $timestamp = time();
        $session_id = $this->generateSessionId($cart, $timestamp);
        $this->persistP24Payment($session_id, $cart->id, $amount, $currency['iso_code'], $timestamp);

        $order = Order::getOrderByCartId($cart->id);
        if($order == null) {
            $s_descr = $this->validatePayment($npsprzelewy24, $cart, $customer, $amount);
            if ($s_descr == null) {
                $this -> errors[] = $m->l('Unable to verify order. Please try to contact the customer support');
                return;
            }
        } else {
            $s_descr = $this->orderDescription($order, $customer);
        }
        $url = Configuration::get('NPS_P24_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_URL');
            $sandbox_descr = Configuration::get('NPS_P24_SANDBOX_ERROR');
            if(!empty($sandbox_descr)) {
                $s_descr = $sandbox_descr;
            }
        }
        $this->transactionRegister($session_id, $url, $cart, $amount, $customer, $currency, $address, $s_descr, $m);
	}

    private function transactionRegister($session_id, $url, $cart, $amount, $customer, $currency, $address, $s_descr, $module) {
        $p24_id = Configuration::get('NPS_P24_MERCHANT_ID');
        $s_lang = new Country((int)($address->id_country));
        $phone = $address->phone;
        if (empty($phone)) {
            $phone = $address->phone_mobile;
        }
        $data = array(
            'p24_merchant_id' => $p24_id,
            'p24_pos_id' => $p24_id,
            'p24_session_id' => $session_id,
            'p24_amount' => $amount,
            'p24_currency' => $currency['iso_code'],
            'p24_description' => $s_descr,
            'p24_email' => $customer->email,
            'p24_address' => $address->address1." ".$address->address2,
            'p24_zip' => $address->postcode,
            'p24_city' => $address->city,
            'p24_country' => $s_lang->iso_code,
            'p24_phone' => $phone,
            'p24_language' => strtolower($s_lang->iso_code),
            'p24_url_cancel' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentCancel'),
            'p24_url_return' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentReturn'),
            'p24_url_status' =>  $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__.'/modules/npsprzelewy24/paymentState.php',
            'p24_shipping' => $cart->getTotalShippingCost(),
            'p24_sign' => $this->generateSign($session_id, $p24_id, $amount, $currency['iso_code']),
            'p24_encoding' => 'UTF-8',
            'p24_api_version' => '3.2',
            'p24_wait_for_result' => 1
        );

        $index = 1;
        foreach ($cart->getProducts() as $product) {
            $data['p24_name_'.$index] = $product['name'];
            $data['p24_quantity_'.$index] = $product['cart_quantity'];
            $data['p24_price_'.$index] = $product['price'];
            $data['p24_number_'.$index] = $product['id_product'];
            $index = $index + 1;
        }

        $ch = curl_init($url.'/trnRegister');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,true);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
        $output=curl_exec($ch);
        curl_close($ch);

        parse_str($output, $result);
        
        if ($result['error'] == 0) {
            $this->payment_url = $url.'/trnRequest/'.$result['token'];
            Tools::redirect($this->payment_url);
        } else {
            $module->reportError(array(
                'Requested URL: '.$url,
                'Request params: '.implode(' | ', $data),
                'Response: '.implode(' | ', $result)
            ));
            Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentReturn', array('p24_error_code' => 'err00')));
        }
    }

    private function persistP24Payment($session_id, $cart_id, $amount, $currency_iso, $timestamp) {
        $p24_state = new P24Payment(null, $cart_id);
        $p24_state->session_id = $session_id;
        $p24_state->id_cart = $cart_id;
        $p24_state->amount = $amount;
        $p24_state->currency_iso = $currency_iso;
        $p24_state->timestamp = $timestamp;
        return $p24_state->save();
    }

    private function generateSessionId($cart, $timestamp) {
        return $cart->id_customer.'|'.$cart->id.'|'.$timestamp;
    }

    private function generateSign($p24_session_id, $p24_merchant_id, $p24_amount, $p24_currency) {
        return md5($p24_session_id.'|'.$p24_merchant_id.'|'.$p24_amount.'|'.$p24_currency.'|'.Configuration::get('NPS_P24_CRC_KEY'));
    }

    private function validatePayment($npsprzelewy24, $cart, $customer, $amount) {
        $result = $npsprzelewy24->validateOrder(
            (int)$cart->id,
            (int)Configuration::get('NPS_P24_ORDER_STATE_1'),
            $amount,
            'przelewy24.pl',
            NULL,
            array(),
            NULL,
            false,
            $customer->secure_key);
        if($result) {
            return $this->orderDescription(Order::getOrderByCartId(intval($cart->id)), $customer);
        } else {
            return null;
        }
    }

    private function orderDescription($orderID, $customer) {
        return $customer->firstname.' '.$customer->lastname.' Order ID: '.$orderID;
    }
}