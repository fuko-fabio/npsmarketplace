<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/npsprzelewy24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistory.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistoryDetail.php');

class P24TransactionDispatcher {

    private $cart;
    private $order;
    private $module;
    private $merchant_spid;
    private $payment_summary;

    public function __construct($id_cart) {
        $this->cart = new Cart($id_cart);
        $this->order = new Order(Order::getOrderByCartId($id_cart));
        $this->module = new NpsPrzelewy24();
        $this->merchant_spid = Configuration::get('NPS_P24_MERCHANT_SPID');
        $this->payment_summary = P24PaymentStatement::getSummaryByCartId($id_cart);
    }

    public function dispatchMoney($retry = false) {
        if (!$this->isMerchanSpidValid() || !$this->isPaymentSummaryValid() || $this->alreadyDispatched($retry))
            return false;

        $total = $this->order->getTotalPaid() * 100;

        $merchant_amount = $total;
        $currencies = $this->module->getCurrency(intval($this->cart->id_currency));
        $date_now = date("Y-m-d H:i:s");
        $currency = $currencies[0];
        $result = array();
        $sellers_invoices_data = array();
        $cart_rules_invoices_data = array();
        $cart_rules = $this->order->getCartRules();

        foreach ($this->order->getProducts() as $product) {
            $id_seller = Seller::getSellerByProduct($product['product_id']);
            if (!$id_seller) {
                PrestaShopLogger::addLog('Unable to find owner of product '.$product['product_name'].'with ID: '.$product['product_id'].' The product will be treated as a store property');
                continue;
            }
            $seller = new Seller($id_seller);
            $spid = P24SellerCompany::getSpidByIdSeller($id_seller);
            if ($spid == null) {
                $this->module->reportError(array(
                    'Unable to dispatch money',
                    'Unable to find seller payment settings. Seller ID: '.$id_seller,
                    'Transaction must by verified manualy'
                ));
                return false;
            }
            $total_product_price = $product['total_price_tax_incl'];
            if (!empty($cart_rules)) {
                $c_r = $this->cartRulesForProduct($cart_rules, $product['id_product']);
                if ($c_r && !empty($c_r)) {
                    $discount = 0;
                    foreach ($c_r as $key => $value) {
                        $discount = $discount + $value['value'];
                    }
                    $total_product_price = $total_product_price - $discount;
                }
                $cart_rules_invoices_data[$seller->id] =  $c_r;
            }

            $a_f_s = $this->amountForSeller($seller, $total_product_price);

            $sellers_invoices_data[] = array(
                'id_seller' => $seller->id,
                'id_product' => $product['product_id'],
                'product_qty' => $product['product_quantity'],
                'id_currency' => intval($this->cart->id_currency),
                'product_total_price' => $total_product_price * 100,
                'commission' => ($total_product_price * 100) - $a_f_s,
                'date' => $date_now,
            );

            if (array_key_exists($spid, $result)) {
                $current_amount = $result[$spid];
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$spid] = $current_amount + $a_f_s;
            } else {
                $merchant_amount = $merchant_amount - $a_f_s;
                $result[$spid] = $a_f_s;
            }
        }

        $message = 'Payment summary for cart with ID: '.$this->cart->id.' | ';
        foreach($result as $key => $value)
            $message = $message.'Seller ID: '.$key.' amount: '.$value.' | ';

        $sellers_amount = 0;
        $sellers_number = count($result);
        foreach($result as $key => $value)
            $sellers_amount = $sellers_amount + $value;

        $history = new P24DispatchHistory(null, $this->payment_summary['id_payment']);

        $available_funds = $this->checkFunds($total);
        if($available_funds == null) {
            $history->total_amount = $total;
            $history->sellers_amount = $sellers_amount;
            $history->sellers_number = $sellers_number;
            $history->date = $date_now;
            $history->id_payment = $this->payment_summary['id_payment'];
            $history->status = false;
            $history->error = $this->module->errorMsg('intErr00');
            $history->save();
            return false;
        }

        $p24_amount = $total - $available_funds;

        $merchant_amount = $merchant_amount - $p24_amount;
        if (array_key_exists($this->merchant_spid, $result)) {
            $ma = $result[$this->merchant_spid] + $merchant_amount;
            $result[$this->merchant_spid] = $ma;
            PrestaShopLogger::addLog('Seller ID equal to Merchant Seller ID: '.$this->merchant_spid.' Merging commision with products amount');
        } else
            $result[$this->merchant_spid] = $merchant_amount;

        $message = $message.'Merchant amount: '.$merchant_amount.' | '.'Total amount: '.($this->order->getTotalPaid() * 100).' | Przelewy24 amount: '.$p24_amount;
        PrestaShopLogger::addLog($message);

        $dispatch_req = array();
        foreach ($result as $key => $value) {
            $dispatch_req[] = array(
                'orderId' => $this->payment_summary['order_id'],
                'sessionId' => $this->payment_summary['session_id'],
                'sellerId' => $key,
                'amount' => $value
            );
        }

        $res = P24::dispatchMoney(time() + (int)$this->cart->id, $dispatch_req);
        if ($res->error->errorCode) {
            $this->module->reportError(array(
                    'Unable to dispatch money',
                    'Przelewy24 service response error code: '.$res->error->errorCode,
                    'Message: '.$res->error->errorMessage
                ));
        }

        $success = $res->error->errorCode ? false : true;

        $history->sellers_amount = $sellers_amount > 0 ? $sellers_amount : 0;
        $history->sellers_number = $sellers_number;
        $history->p24_amount = $p24_amount > 0 ? $p24_amount : 0;
        $history->merchant_amount = $merchant_amount > 0 ? $merchant_amount : 0;
        $history->total_amount = $total > 0 ? $total : 0;
        $history->date = $date_now;
        $history->id_payment = $this->payment_summary['id_payment'];
        $h_s = $success;
        foreach ($res->result as $r) {
            $h_s = $h_s && $r->status;
        }
        $history->status = $h_s;
        $history->error = $res->error->errorMessage;
        $history->save();

        foreach ($res->result as $r) {
            $h = new P24DispatchHistoryDetail();
            $h->id_p24_dispatch_history = $history->id;
            $h->id_seller = P24SellerCompany::getIdSellerBySpid($r->sellerId);
            $h->session_id = $r->sessionId;
            $h->spid = $r->sellerId;
            $h->amount = $r->amount > 0 ? $r->amount : 0;
            $h->status = $r->status;
            $h->error = $r->error;
            $h->merchant = $r->sellerId == $this->merchant_spid;
            $h->save();
        }
        
        if ($success) {
            $this->persistInvoicesData($sellers_invoices_data);
        }
        return $success;
    }

    private function persistInvoicesData($sellers_invoices_data) {
        foreach ($sellers_invoices_data as $data) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'seller_invoice_data`
            (`id_seller`, `id_product`, `id_currency`, `product_qty`, `product_total_price`, `commission`, `date`)
            VALUES ('.$data['id_seller']
            .','.$data['id_product']
            .','.$data['id_currency']
            .','.$data['product_qty']
            .','.$data['product_total_price']
            .','.$data['commission']
            .',\''.$data['date'].'\')';
            Db::getInstance()->execute($sql);
        }
    }

    private function amountForSeller($seller, $amount) {
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

    private function cartRulesForProduct($cart_rules, $id_product) {
        if (Module::isInstalled('npsvouchers')) {
            $ids = array();
            foreach ($cart_rules as $key => $value) {
                $ids[] = $value['id_cart_rule'];
            }
            if (!empty($ids)) {
                $dbquery = new DbQuery();
                $dbquery->select('ocr.value, ocr.name')
                    ->from('order_cart_rule', 'ocr')
                    ->leftJoin('order_seller_cart_rule', 'oscr', 'ocr.id_order_cart_rule = oscr.id_order_cart_rule')
                    ->where('ocr.`id_cart_rule` IN('.implode(',', $ids).') AND ocr.`id_order` = '.$this->order->id.' AND oscr.`id_product` = '.$id_product);
                return Db::getInstance()->executeS($dbquery);
            }
        }
        return array();
    }

    private function checkFunds($total) {
        $res = P24::checkFunds($this->payment_summary['order_id'], $this->payment_summary['session_id']);
        if ($res->error->errorCode) {
            $this->module->reportError(array(
                    'Unable to dispatch money',
                    'Cannot check available funds',
                    'Przelewy24 service response error code: '.$res->error->errorCode,
                    'Message: '.$res->error->errorMessage
            ));
            return null;
        }
        if ($res->result > $total) {
            $this->module->reportError(array(
                    'Unable to dispatch money',
                    'Account balance is greater than total amount to dispatch',
                    'Total: '.($total/100).' Account balance: '.($res->result/100)
            ));
            return null;
        }
        return $res->result;
    }

    private function isMerchanSpidValid() {
        if (empty($this->merchant_spid)) {
            $this->module->reportError(array(
                'Unable to dispatch money',
                'Unable to find merchant Przelewy24 Seller ID. Check your Przelewy24 module configuration',
                'Verifiy transaction manualy'
            ));
            return false;
        }
        return true;
    }

    private function isPaymentSummaryValid() {
        if (!$this->payment_summary) {
            $this->module->reportError(array(
                'Unable to dispatch money',
                'Unable to find payment and payment statement entry in database',
                'Verifiy transaction manualy'
            ));
            return false;
        }
        return true;
    }

    private function alreadyDispatched($retry) {
        $history = new P24DispatchHistory(null, $this->payment_summary['id_payment']);
        if ($history->id != null && !$retry) {
            $this->module->reportError(array(
                'Unable to dispatch money',
                'Money already dispatched.',
                'Verifiy transaction manualy'
            ));
            return true;
        }
        return false;
    }
}
