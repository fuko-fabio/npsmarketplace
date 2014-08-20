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

        $order = Order::getOrderByCartId($cart->id);
        if($order == null){
            $s_descr = '';
            $validationRequired = true;
        } else {
            $s_descr = 'Zamówienie: '.$order;
            $validationRequired = false;
        }

        $url = Configuration::get('NPS_P24_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_URL');
        }
        $this->transactionRegister($url, $cart, $amount, $customer, $currency, $address, $s_descr);
        #Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentError', array('main_error' => 'trn_access_error')));
	}

    private function transactionRegister($url, $cart, $amount, $customer, $currency, $address, $s_descr) {
        $p24_id = Configuration::get('NPS_P24_MERCHANT_ID');
        $session_id = $this->generateSessionId($cart);
        $this->persistSssionId($session_id, $cart->id, $amount);
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
        d($data);
        return;
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, count($data));

        $output=curl_exec($ch);
        curl_close($ch);
        d($output);
        return $output;
    }

    private function persistSssionId($session_id, $cart_id, $amount) {
        return Db::getInstance()->Execute(
          'INSERT INTO `'._DB_PREFIX_.'przelewy24_amount` '.'(`s_sid`,`i_id_order`,`i_amount`)
          VALUES("'.$session_id.'",'.$cart_id.','.$amount.')');
    }

    private function generateSessionId($cart) {
        return md5($cart->id_customer.'|'.$cart->id.'|'.time());
    }

    private function generateSign($p24_session_id, $p24_merchant_id, $p24_amount, $p24_currency) {
        return md5($p24_session_id.'|'.$p24_merchant_id.'|'.$p24_amount.'|'.$p24_currency.'|'.Configuration::get('NPS_P24_CRC_KEY'));
    }

    //TODO Can be used??
    private function soapTransactionRegister($soap, $p24_id, $p24_key) {
        $transaction = array(
            'sessionId' => $cart->id.'|'.$s_sid,
            'email' => $customer->email,
            'amount' => $amount,
            'methodId' => 0,
            'client' => $customer->firstname.' '.$customer->lastname,
            'street' => $address->address1." ".$address->address2,
            'city' => $address->city,
            'zip' => $address->postcode,
            'country' => $s_lang->iso_code,
            'description' => $s_descr
        );
        $res = $soap->TrnRegister(Configuration::get('NPS_P24_MERCHANT_ID'), Configuration::get('NPS_P24_UNIQUE_KEY'), $transaction);
        // $res->result contains data about transaction, or object with empty field in case of error
        if ($res->error->errorCode) {
            Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentError', array('main_error' => 'trn_register_error', 'p24_error' => $res->error)));
        } else {
            # Result
            # orderId (int)
            # orderIdFull (int),
            # sessionId (string),
            # ban (string),
            # banOwner (string),
            # banOwnerAddress (string),
            # amount (int)
            $this->transactionRequest($soap, $p24_id, $p24_key);
            echo 'Transaction data: ';
            $T = $res->result;
            $orderId = $T->orderId; // or $T->orderIdFull
            $IBAN = $T->ban;
            echo 'You should pay to: ' . $IBAN . ', with title: p24-' . $orderId;
            Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentError', array('main_error' => 'trn_register_error', 'p24_error' => $res->error)));
            
        }
    }
}