<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistory.php');

class P24TransationDispatcher {

    public static function dispatchMoney($id_cart) {
        $npsprzelewy24 = new NpsPrzelewy24();
        $merchant_spid = Configuration::get('NPS_P24_MERCHANT_SPID');

        $cart = new Cart($id_cart);
        $merchant_amount = $cart->getOrderTotal() * 100;
        $currencies = $npsprzelewy24->getCurrency(intval($cart->id_currency));
        $currency = $currencies[0];
        $result = array();

        $payment_summary = P24PaymentStatement::getSummary($id_cart);
        if(!$payment_summary) {
            $npsprzelewy24->reportError(array(
                'Unable to dispatch money',
                'Unable to find payment and payment statement entry in database',
                'Transaction must by verified manualy'
            ));
            return;
        }
        foreach ($cart->getProducts() as $product) {
            $id_seller = Seller::getSellerByProduct($product['id_product']);
            if (!$id_seller) {
                $npsprzelewy24->reportError(array(
                    'Unable to dispatch money',
                    'Unable to find owner of product '.$product['name'].'with ID: '.$product['id_product'],
                    'Transaction must by verified manualy'
                ));
                return;
            }
            $seller = new Seller($id_seller);
            $s_p_s = new P24SellerCompany(null, $id_seller);

            if (array_key_exists($s_p_s->spid, $result)) {
                $current_amount = $result[$s_p_s->spid];
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$s_p_s->spid] = $current_amount + $a_f_s;
            } else {
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$s_p_s->spid] = $a_f_s;
            }
        }
        $message = 'Payment summary for cart with ID: '.$cart->id.' | ';
        foreach($result as $key => $value)
            $message = $message.'Seller ID: '.$key.' amount: '.$value.' | ';

        if (!empty($merchant_spid))
            $result[$merchant_spid] = $merchant_amount;
        else
            PrestaShopLogger::addLog('Przelewy24 merchant SPID not specified. Not dispatched amount: $merchant_amount' , 2);

        $sum = 0;
        foreach ($result as $key => $value) {
            $sum = $sum + $value;
        }
        $message = $message.'Merchant amount: '.$merchant_amount.' | '.'Total amount: '.($cart->getOrderTotal() * 100).' | Total calculated amount: '.$sum;
        PrestaShopLogger::addLog($message);

        $dispatch_req = array();
        foreach ($result as $key => $value) {
            $dispatch_req[] = array(
                'orderId' => $payment_summary['order_id'],
                'sessionId' => P24TransationDispatcher::generateSessionId($cart, $payment_summary['order_id'], $key),
                'sellerId' => $key,
                'amount' => $value
            );
        }

        $res = P24::dispatchMoney((int)$cart->id, $dispatch_req);
        if ($res->error->errorCode) {
            $npsprzelewy24->reportError(array(
                    'Unable to dispatch money',
                    'Przelewy24 service response error code: '.$res->error->errorCode,
                    'Message: '.$res->error->errorMessage
                ));
        }
        foreach ($res->result as $r) {
            $h = new P24DispatchHistory();
            $h->id_payment = $payment_summary['id_payment'];
            $h->order_id = $r->orderId;
            $h->session_id = $r->sessionId;
            $h->spid = $r->sellerId;
            $h->amount = $r->amount;
            $h->status = $r->status;
            $h->error = $r->error;
            $h->date = date("Y-m-d H:i:s");
            $h->merchant = $r->sellerId == $merchant_spid;
            $h->save();
        }
    }

    private static function amountForSeller($seller, $amount) {
        $p24_commision = Configuration::get('NPS_P24_COMMISION');
        $result = $amount - ($amount * (($seller->commision + $p24_commision)/ 100));// TODO Jak to wlasciwie policzyc?
        if (isset($currency['decimals']) && $currency['decimals'] == '0') {
            if (Configuration::get('PS_PRICE_ROUND_MODE') != null) {
                switch (Configuration::get('PS_PRICE_ROUND_MODE')) {
                    case 0:
                        $result = ceil($result);
                        break;
                    case 1:
                        $result = floor($result);
                        break;
                    case 2:
                        $result = round($result);
                        break;
                }
            }
        }
        return ceil($result * 100); // TODO zaokraglanie jak???
    }

    private static function generateSessionId($cart, $order_id, $spid) {
        return md5($cart->id_customer.'|'.$cart->id.'|'.$order_id.'|'.$spid);
    }
}