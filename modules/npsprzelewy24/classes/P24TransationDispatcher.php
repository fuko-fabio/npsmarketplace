<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistory.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistoryDetail.php');

class P24TransationDispatcher {

    public static function dispatchMoney($id_cart) {
        $npsprzelewy24 = new NpsPrzelewy24();
        $merchant_spid = Configuration::get('NPS_P24_MERCHANT_SPID');
        if (empty($merchant_spid)) {
            $npsprzelewy24->reportError(array(
                'Unable to dispatch money',
                'Unable to find merchant Przelewy24 Seller ID. Chec your Przelewy24 module configuration',
                'Transaction must by verified manualy'
            ));
            return;
        }
        $cart = new Cart($id_cart);
        $total = $cart->getOrderTotal() * 100;
        $merchant_amount = $total;
        $currencies = $npsprzelewy24->getCurrency(intval($cart->id_currency));
        $currency = $currencies[0];
        $result = array();

        $payment_summary = P24PaymentStatement::getSummaryByCartId($id_cart);
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
            $spid = P24SellerCompany::getSpidByIdSeller($id_seller);
            if ($spid == null) {
                $npsprzelewy24->reportError(array(
                    'Unable to dispatch money',
                    'Unable to find seller payment settings. Seller ID: '.$id_seller,
                    'Transaction must by verified manualy'
                ));
                return;
            }
            if (array_key_exists($spid, $result)) {
                $current_amount = $result[$spid];
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$spid] = $current_amount + $a_f_s;
            } else {
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$spid] = $a_f_s;
            }
        }
        $message = 'Payment summary for cart with ID: '.$cart->id.' | ';
        foreach($result as $key => $value)
            $message = $message.'Seller ID: '.$key.' amount: '.$value.' | ';

        $sellers_amount = 0;
        $sellers_number = count($result);
        foreach($result as $key => $value)
            $sellers_amount = $sellers_amount + $value;

        $p24_amount = ceil(($total * Configuration::get('NPS_P24_COMMISION')) / 100);
        $merchant_amount = $merchant_amount - $p24_amount;
        if (array_key_exists($merchant_spid, $result)) {
            $ma = $result[$merchant_spid] + $merchant_amount;
            $result[$merchant_spid] = $ma;
            PrestaShopLogger::addLog('Seller ID equal to Merchant Seller ID: '.$merchant_spid.' Merging commision with products amount');
        } else
            $result[$merchant_spid] = $merchant_amount;

        $message = $message.'Merchant amount: '.$merchant_amount.' | '.'Total amount: '.($cart->getOrderTotal() * 100).' | Przelewy24 amount: '.$p24_amount;
        PrestaShopLogger::addLog($message);

        $dispatch_req = array();
        foreach ($result as $key => $value) {
            $dispatch_req[] = array(
                'orderId' => $payment_summary['order_id'],
                'sessionId' => $payment_summary['session_id'],
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

        $history = new P24DispatchHistory();
        $history->sellers_amount = $sellers_amount;
        $history->sellers_number = $sellers_number;
        $history->p24_amount = $p24_amount;
        $history->merchant_amount = $merchant_amount;
        $history->total_amount = $total;
        $history->date = date("Y-m-d H:i:s");
        $history->id_payment = $payment_summary['id_payment'];
        $history->status = $res->result[0]->status;
        $history->save();

        foreach ($res->result as $r) {
            $h = new P24DispatchHistoryDetail();
            $h->id_p24_dispatch_history = $history->id;
            $h->id_seller = P24SellerCompany::getIdSellerBySpid($r->sellerId);
            $h->session_id = $r->sessionId;
            $h->spid = $r->sellerId;
            $h->amount = $r->amount;
            $h->status = $r->status;
            $h->error = $r->error;
            $h->merchant = $r->sellerId == $merchant_spid;
            $h->save();
        }
    }

    private static function amountForSeller($seller, $amount) {
        $p24_commision = Configuration::get('NPS_P24_COMMISION');
        $result = $amount - ($amount * (($seller->commision + $p24_commision)/ 100));
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
        return floor($result * 100);
    }
}