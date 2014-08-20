<?php

class NpsPrzelewy24PaymentConfirmationModuleFrontController extends ModuleFrontController {

    public function init() {
        parent::init();

        if(isset($_GET['order_id'])) {
            $cart = Cart::getCartByOrderId($_GET['order_id']);
            if($cart == null) {
                die();
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
        $session_id = $this->generateSessionId($cart);
        $this->persistSssionId($session_id, $cart->id, $amount);

        $order = Order::getOrderByCartId($cart->id);
        if($order == null) {
            $s_descr = $this->validatePayment($npsprzelewy24, $cart, $customer, $amount);
        } else {
            $s_descr = $this->orderDescription($order);
        }

        $url = Configuration::get('NPS_P24_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_URL');
        }

        $this->transactionRegister($session_id, $url, $cart, $amount, $customer, $currency, $address, $s_descr);
	}

    private function transactionRegister($session_id, $url, $cart, $amount, $customer, $currency, $address, $s_descr) {
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
            'p24_url_cancel' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentCancell'),
            'p24_url_return' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentSuccessful'),
            'p24_url_status' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentStatus'),
            'p24_shipping' => $cart->getTotalShippingCost(),
            'p24_sign' => $this->generateSign($session_id, $p24_id, $amount, $currency['iso_code']),
            'p24_encoding' => 'UTF-8',
            'p24_api_version' => '3.2',
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
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //$verbose = fopen('php://temp', 'rw+');
        //curl_setopt($ch, CURLOPT_STDERR, $verbose);
        $output=curl_exec($ch);
        curl_close($ch);
        //rewind($verbose);
        //$verboseLog = stream_get_contents($verbose);

        parse_str($output, $result);
        
        if ($result['error'] == 0) {
            Tools::redirect($url.'/trnRequest/'.$result['token']);
        } else {
            Tools::displayError('Unable to register transaction'); // TODO Redirect to page
        }
    }

    private function persistSssionId($session_id, $cart_id, $amount) {
        return Db::getInstance()->Execute(
          'INSERT INTO `'._DB_PREFIX_.'przelewy24_amount` '.'(`s_sid`,`i_id_order`,`i_amount`)
          VALUES("'.$session_id.'",'.$cart_id.','.$amount.')');
    }

    private function generateSessionId($cart) {
        return $cart->id_customer.'|'.$cart->id.'|'.md5(time());
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

        $orderID = Order::getOrderByCartId(intval($cart->id));
        if ($orderID == null) {
            d('Error');
        } else {
            return $this->orderDescription($orderID);
        }
    }

    private function orderDescription($orderID) {
        return 'Zamówienie: '.$orderID;
    }
}