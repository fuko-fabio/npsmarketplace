<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24TransationDispatcher {

    public static function dispatchMoney() {
        $cart = new Cart($id_cart);
        $result = array();
        foreach ($cart->getProducts() as $product) {
            $id_seller = Seller::getSellerByProduct($product['id_product']);
            if (!$id_seller) {
                d('Seller not exists');
                return; //TODO
            }
            $seller = new Seller($id_seller);
            $s_p_s = new P24SellerSettings(null, $id_seller);

            if (array_key_exists($s_p_s->spid)) {
                $current_amount = $result[$s_p_s->spid];
                $result[$s_p_s->spid] = $current_amount + $this->amountForSeller($seller, $product['price']);
            } else {
                $result[$s_p_s->spid] = $this->amountForSeller($seller, $product['price']);
            }
        }
    }

    private static function amountForSeller($seller, $amount) {
        $p24_commision = 2.5; //TODO 
        return $amount - ($amount * (($seller->commision + $p24_commision)/ 100));
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