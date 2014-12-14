<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24ErrorMessage.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');

class NpsPrzelewy24PaymentResultModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $p24_error_code = Tools::getValue('p24_error_code');
        $p24_session_id = Tools::getValue('p24_session_id');

        if (empty($p24_error_code) && isset($p24_session_id) && !empty($p24_session_id)) {
            $session_id_array = explode('|', $p24_session_id);
            $id_cart = $session_id_array[1];
            $p24_amount = Tools::getValue('p24_amount');
            $p24_currency = Tools::getValue('p24_currency');
            $p24_order_id = Tools::getValue('p24_order_id');
            $p24_method = Tools::getValue('p24_method');
            $p24_statement = Tools::getValue('p24_statement');
            $p24_sign = Tools::getValue('p24_sign');
            $p24_token = Tools::getValue('p24_token');

            $validator = new P24PaymentValodator(
                $p24_session_id,
                $p24_amount,
                $p24_currency,
                $p24_order_id,
                $p24_method,
                $p24_statement,
                $p24_sign
            );

            $result = $validator->validate($p24_token);
            if ($result['error'] == 0) {
                $id_order = Order::getOrderByCartId($id_cart);
                $order = P24Payment::getSummaryByCartId($id_cart);
                $price = Tools::displayPrice($order['amount'] / 100, $this->context->currency);
                $this->context->smarty->assign(array(
                    'order' => $order,
                    'price' => $price,
                    'reference_order' => Order::getUniqReferenceOf($id_order)
                ));
                $dispatcher = new P24TransactionDispatcher($id_cart);
                $dispatcher->dispatchMoney();
            } else {
                if ($result['error'] == 1)
                    $this->persistPaymentError($p24_session_id);
                $this->context->smarty->assign(array(
                    'error' => array('code' => $result['error'], 'message' => $result['errorMessage']),
                ));
            }
        } else {
            $this->module->reportError(array(
                'Requested URL: '.$this->context->link->getModuleLink('npsprzelewy24', 'paymentResult'),
                'GET params: '.implode(' | ', $_GET),
                'POST params: '.implode(' | ', $_POST),
            ));
            $this->persistPaymentError($p24_session_id);
            $this->context->smarty->assign(array(
                'error' => array(
                    'code' => $p24_error_code,
                    'message' => P24ErrorMessage::get($p24_error_code)),
            ));
        }
        $this->setTemplate('payment_return.tpl');
    }

    private function persistPaymentError($p24_session_id) {
        if (isset($p24_session_id) && !empty($p24_session_id)) {
            $session_id_array = explode('|', $p24_session_id);
            $id_cart = $session_id_array[1];
            $id_order = Order::getOrderByCartId($id_cart);
            if(isset($id_order)) {
                $history = new OrderHistory();
                $history->id_order = intval($id_order);
                $history->changeIdOrderState(8, intval($id_order));
                $history->addWithemail(true);
            }
        }
    }
}