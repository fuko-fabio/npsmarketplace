<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');

class NpsPrzelewy24PaymentConfirmationModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $this->setTemplate('payment_confirmation.tpl');

        $npsprzelewy24 = new NpsPrzelewy24();
        if(isset($_GET['order_id'])) {
            $cart = Cart::getCartByOrderId($_GET['order_id']);
            if($cart == null) {
                $this -> errors[] = sprintf($npsprzelewy24->l('Requested order with id %s not exists. Please try to contact the customer support'), $_GET['order_id']);
                return;
            }
        } else {
            global $cart;
        }

        $payment = P24Payment::getByCartId($cart->id);
        if ($payment != null && $payment->id != null) {
            $this -> errors[] = $npsprzelewy24->l('Payment already finalized. Go to your account and check orders history.');
            return;
        }

        $address = new Address((int)$cart->id_address_invoice);
        $customer = new Customer((int)($cart->id_customer));
        $amount = $cart->getOrderTotal(true, Cart::BOTH);
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

        $order = Order::getOrderByCartId($cart->id);
        if($order == null) {
            $s_descr = $this->validatePayment($npsprzelewy24, $cart, $customer, $amount);
            if ($s_descr == null) {
                $this -> errors[] = $npsprzelewy24->l('Unable to verify order. Please contact with customer support');
                return;
            }
        } else {
            $s_descr = $this->orderDescription($order, $customer);
        }

        $this->persistP24Payment($session_id, $cart->id, $amount, $currency['iso_code'], $timestamp);
        $this->transactionRegister($session_id, $cart, $amount, $customer, $currency, $address, $s_descr, $npsprzelewy24);
    }

    private function transactionRegister($session_id, $cart, $amount, $customer, $currency, $address, $s_descr, $module) {
        $p24_id = P24::merchantId();
        $p24_token = Tools::encrypt($session_id); 
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
            'p24_url_cancel' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentCancel', array('p24-token' => $p24_token)),
            'p24_url_return' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentReturn', array('p24-token' => $p24_token)),
            'p24_url_status' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/npsprzelewy24/paymentState.php?p24-token='.$p24_token,
            'p24_shipping' => $cart->getTotalShippingCost(),
            'p24_sign' => $this->generateSign($session_id, $p24_id, $amount, $currency['iso_code']),
            'p24_encoding' => 'UTF-8',
            'p24_api_version' => '3.2',
            'p24_wait_for_result' => 1
        );

        $shop_name = Configuration::get('PS_SHOP_NAME');
        $index = 1;
        foreach ($cart->getProducts() as $product) {
            $data['p24_name_'.$index] = $product['name'];
            $data['p24_quantity_'.$index] = $product['cart_quantity'];
            $data['p24_price_'.$index] = $product['price'];
            $data['p24_number_'.$index] = $product['id_product'];
            $data['p24_description_'.$index] = $shop_name.' Seller ID: '.Seller::getSellerByProduct($product['id_product']);
            $index = $index + 1;
        }
        $result = P24::transactionRegister($data);

        if ($result['error'] == 0) {
            Tools::redirect(P24::url().'/trnRequest/'.$result['token']);
        } else {
            $order = Order::getOrderByCartId($cart->id);
            if($order) {
                $history = new OrderHistory();
                $history->id_order = intval($order_id);
                $history->changeIdOrderState(8, intval($order['id_order']));
            }
            $module->reportError(array(
                'Requested URL: '.P24::url().'/trnRegister',
                'Request params: '.implode(' | ', $data),
                'Response: '.implode(' | ', $result)
            ));
            $payment = new P24Payment(null, $cart->id);
            if ($payment->id != null) {
                $payment->delete();
            }
            $this -> errors[] = $npsprzelewy24->l('Unable to register transaction in Przelewy24 service. Please contact with customer support');
        }
    }

    private function persistP24Payment($session_id, $cart_id, $amount, $currency_iso, $timestamp) {
        $p24_state = new P24Payment(null, $cart_id);
        $p24_state->session_id = $session_id;
        $p24_state->id_cart = $cart_id;
        $p24_state->amount = $amount;
        $p24_state->currency_iso = $currency_iso;
        $p24_state->timestamp = $timestamp;
        $p24_state->token = Tools::encrypt($session_id);
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
            (int)Configuration::get('NPS_P24_ORDER_STATE_AWAITING'),
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