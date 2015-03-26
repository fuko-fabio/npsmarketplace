<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');

class NpsPrzelewy24PaymentConfirmationModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'order-follow';
    public $ssl = true;

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        $this->setTemplate('payment_confirmation.tpl');
        $is_renew = Tools::getValue('renew');

        if(isset($_GET['order_id'])) {
            $cart = Cart::getCartByOrderId($_GET['order_id']);
            if($cart == null) {
                $this->errors[] = sprintf($this->module->l('Requested order with id %s not exists. Please try to contact the customer support', 'paymentConfirmation'), $_GET['order_id']);
                return;
            }
        } else {
            global $cart;
        }

        $payment = new P24Payment(null, $cart->id);
        if ($payment->id != null && !$is_renew) {
            $this->errors[] = $this->module->l('Payment already finalized. Go to your account and check orders history.', 'paymentConfirmation');
            return;
        }

        $address = new Address((int)$cart->id_address_invoice);
        $customer = new Customer((int)($cart->id_customer));
        $amount = $this->roundPrice($cart->getOrderTotal(true, Cart::BOTH));
        $currencies = $this->module->getCurrency(intval($cart->id_currency));
        $currency = $currencies[0];

        $id_order = Order::getOrderByCartId($cart->id);
        if (!$id_order) {
            $s_descr = $this->validatePayment($cart, $customer, $amount);
            if ($s_descr == null) {
                $this -> errors[] = $this->module->l('Unable to verify order. Please contact with customer support', 'paymentConfirmation');
                return;
            }
        } else {
            $s_descr = $this->orderDescription($id_order, $customer);
            $order = new Order($id_order);
            $amount = $order->getTotalPaid();
        }

        $this->transactionRegister($payment, $cart, $amount, $customer, $currency, $address, $s_descr);
    }

    private function roundPrice($amount) {
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
        return $amount;
    }

    /* Used for tests, do not use on production */
    private function updateOrderState($id_cart) {
        $order = new Order(Order::getOrderByCartId($id_cart));
        $order_state = Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED');

        $history = new OrderHistory();
        $history->id_order = $order->id;
        $history->changeIdOrderState($order_state, intval($order->id), true);
        $history->add();

        $payments = $order->getOrderPaymentCollection();
        if (count($payments) > 0) {
            $payments[0]->transaction_id = $id_cart;
            $payments[0]->update();
        }
    }

    private function transactionRegister(P24Payment $payment, Cart $cart, $amount, Customer $customer, $currency, Address $address, $s_descr) {
        $p24_id = P24::merchantId();
        $amount = number_format($amount, 2, '.', '') * 100; // From float to int: 10.50 -> 1050
        $timestamp = time();
        $session_id = $this->generateSessionId($cart, $timestamp);
        $p24_token = Tools::encrypt($session_id);
        $p24_sign = $this->generateSign($session_id, $p24_id, $amount, $currency['iso_code']);

        $s_lang = new Country((int)($address->id_country));
        $phone = $address->phone;
        if (empty($phone)) {
            $phone = $address->phone_mobile;
        }
        $return_url = $this->context->link->getModuleLink('npsprzelewy24', 'paymentResult', array('id_cart' => $cart->id));
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $return_url = $this->context->link->getModuleLink('npsprzelewy24', 'paymentResult', array('id_cart' => $cart->id, 'p24_token' => $p24_token));
        }
        $data = array(
            'p24_merchant_id' => $p24_id,
            'p24_pos_id' => $p24_id,
            'p24_session_id' => $session_id,
            'p24_amount' => $amount,
            'p24_currency' => $currency['iso_code'],
            'p24_country' => $s_lang->iso_code,
            'p24_language' => strtolower($s_lang->iso_code),
            'p24_sign' => $p24_sign,
            'p24_encoding' => 'UTF-8',
            'p24_api_version' => '3.2',
            'p24_wait_for_result' => 1,
            'p24_description' => $s_descr,
            'p24_phone' => $phone,
            'p24_email' => $customer->email,
            'p24_address' => $address->address1." ".$address->address2,
            'p24_zip' => $address->postcode,
            'p24_city' => $address->city,
            'p24_url_cancel' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentCancel'),
            'p24_url_return' => $return_url,
            'p24_url_status' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/npsprzelewy24/paymentState.php?p24_token='.$p24_token,
            'p24_shipping' => $cart->getTotalShippingCost(),
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

        $payment->session_id = $session_id;
        $payment->id_cart = $cart->id;
        $payment->amount = $amount;
        $payment->currency_iso = $currency['iso_code'];
        $payment->timestamp = $timestamp;
        $payment->token = $p24_token;
        if (!$payment->save()) {
            $this->errors[] = $this->module->l('Unable to finalize order. Please contact with customer support.', 'paymentConfirmation');
            return;
        }
        PrestaShopLogger::addLog('Registering P24 payment:'.implode(' | ', $data), 1);

        $result = P24::transactionRegister($data);
        $order = new Order(Order::getOrderByCartId($cart->id));

        if (isset($result) && $result['error'] == 0) {
            if ($order->current_state == 8) {
                $history = new OrderHistory();
                $history->id_order = $order->id;
                $history->changeIdOrderState((int)Configuration::get('NPS_P24_ORDER_STATE_AWAITING'), $order->id, true);
                $history->add();
            }
            Tools::redirect(P24::url().'/trnRequest/'.$result['token']);
        } else {
            $history = new OrderHistory();
            $history->id_order = $order->id;
            $history->changeIdOrderState(8, $order->id, true);
            $history->addWithemail(true);
            $this->module->reportError(array(
                'Requested URL: '.P24::url().'/trnRegister',
                'Request params: '.implode(' | ', $data),
                'Response: '.implode(' | ', $result)
            ));
            $payment->delete();
            Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentCancel', array('error' => true)));
        }
    }

    private function generateSessionId($cart, $timestamp) {
        return $cart->id_customer.'|'.$cart->id.'|'.$timestamp;
    }

    private function generateSign($p24_session_id, $p24_merchant_id, $p24_amount, $p24_currency) {
        return md5($p24_session_id.'|'.$p24_merchant_id.'|'.$p24_amount.'|'.$p24_currency.'|'.Configuration::get('NPS_P24_CRC_KEY'));
    }

    private function validatePayment($cart, $customer, $amount) {
        $result = $this->module->validateOrder(
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