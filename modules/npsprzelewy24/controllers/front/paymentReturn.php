<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24ErrorMessage.php');

class NpsPrzelewy24PaymentReturnModuleFrontController extends ModuleFrontController {

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $m = new NpsPrzelewy24();
        $p24_error_code = Tools::getValue('p24_error_code');
        $session_id_array = explode('|', Tools::getValue('p24_session_id'));
        $id_cart = $session_id_array[1];
        $id_order = Order::getOrderByCartId($id_cart);
        if (empty($p24_error_code)) {
            $validator = new P24PaymentValodator(
                Tools::getValue('p24_merchant_id'),
                Tools::getValue('p24_pos_id'),
                Tools::getValue('p24_session_id'),
                Tools::getValue('p24_amount'),
                Tools::getValue('p24_currency'),
                Tools::getValue('p24_order_id'),
                Tools::getValue('p24_method'),
                Tools::getValue('p24_statement'),
                Tools::getValue('p24_sign')
            );

            $result = $validator->validate();
            if ($result['error'] == 0) {
                $order = P24Payment::getSummaryByCartId($id_cart);
                $price = Tools::displayPrice($order['amount'] / 100, $this->context->currency);
                $this->context->smarty->assign(array(
                    'order' => $order,
                    'price' => $price,
                    'reference_order' => Order::getUniqReferenceOf($id_order)
                ));
                P24TransactionDispatcher::dispatchMoney($id_cart);
            } else {
                $this->persistPaymentError($order_id);
                $this->context->smarty->assign(array(
                    'error' => array('code' => $result['error'], 'message' => $result['errorMessage']),
                ));
            }
        } else {
            $this->persistPaymentError($order_id);
            $m->reportError(array(
                'Requested URL: '.$this->context->link->getModuleLink('npsprzelewy24', 'paymentReturn'),
                'GET params: '.implode(' | ', $_GET),
                'POST params: '.implode(' | ', $_POST),
            ));
            $this->context->smarty->assign(array(
                'error' => array(
                    'code' => $p24_error_code,
                    'message' => P24ErrorMessage::get($p24_error_code)),
            ));
        }
        $this->setTemplate('payment_return.tpl');
    }

    private function persistPaymentError($order_id) {
        $history = new OrderHistory();
        $history->id_order = intval($order_id);
        $history->changeIdOrderState(8, intval($order_id));
        $history->addWithemail(true);
    }
}