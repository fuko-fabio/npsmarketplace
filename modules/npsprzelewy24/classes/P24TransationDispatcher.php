<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class P24TransationDispatcher {

    public static function dispatchMoney($id_cart) {
        $npsprzelewy24 = new NpsPrzelewy24();
        $cart = new Cart($id_cart);
        $total_amount = $cart->getOrderTotal() * 100;
        $currencies = $npsprzelewy24->getCurrency(intval($cart->id_currency));
        $currency = $currencies[0];
        $result = array();
        foreach ($cart->getProducts() as $product) {
            $id_seller = Seller::getSellerByProduct($product['id_product']);
            if (!$id_seller) {
                d('Seller not exists');
                return; //TODO
            }
            $seller = new Seller($id_seller);
            $s_p_s = new P24SellerCompany(null, $id_seller);

            if (array_key_exists($s_p_s->spid, $result)) {
                $current_amount = $result[$s_p_s->spid];
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $total_amount = $total_amount - $a_f_s;
                $result[$s_p_s->spid] = $current_amount + $a_f_s;
            } else {
                $a_f_s = P24TransationDispatcher::amountForSeller($seller, $product['price']);
                $total_amount = $total_amount - $a_f_s;
                $result[$s_p_s->spid] = $a_f_s;
            }
        }
        $result['merchant_amount'] = $total_amount;
        
        $sum = 0;
        foreach ($result as $key => $value) {
            $sum = $sum + $value;
        }
        $result['total'] = $cart->getOrderTotal() * 100;
        $result['sum'] = $sum; 
        d($result);
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
        return ceil($result * 100);
    }

    private static function transactionDispatch() {
        $soap = new SoapClient("https://secure.przelewy24.pl/external/wsdl/service.php?wsdl");
        $dispatchReq = array( 0 => array (
        'orderId' => 929048, 'sessionId' => 'na39biwefipc5b6ghmlrjc0',
        'sellerId' => 11223, 'amount' => 900
        ));
        $res = $soap->TrnDispatch('9999', 'anuniquekeyretrievedfromprzelewy24', 10, $dispatchReq);
        // $res->result contains data about rates
        if ($res->error->errorCode) {
        echo 'Something went wrong: ' . $res->error->errorMessage;
        } else {
        if ($res->result[0]->status) echo 'Dispatch OK!';
        }
    }
}