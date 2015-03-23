<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');

class NpsPrzelewy24PaymentResultModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            PrestaShopLogger::addLog('Starting payment validation. GET params: '.implode(' | ', $_GET).' POST params: '.implode(' | ', $_POST));
            $this->module->validateP24Response(
                Tools::getValue('p24_error_code'),
                Tools::getValue('p24_token'),
                Tools::getValue('p24_session_id'),
                Tools::getValue('p24_amount'),
                Tools::getValue('p24_currency'),
                Tools::getValue('p24_order_id'),
                Tools::getValue('p24_method'),
                Tools::getValue('p24_statement'),
                Tools::getValue('p24_sign')
            );
        }

        $id_cart = Tools::getValue('id_cart');
        if (isset($id_cart) && !empty($id_cart)) {
            $id_order = Order::getOrderByCartId($id_cart);
            $order = new Order($id_order);
            $this->context->smarty->assign(array(
                'price' => $order->getTotalPaid($this->context->currency),
                'order_reference' => $order->getUniqReference(),
                'order_url' => $this->context->link->getPageLink('order-detail', null, null, array('id_order' => $id_order)),
                'currency' => $this->context->currency,
            ));
            $payment_summary = P24PaymentStatement::getSummaryByCartId($id_cart);
            if ($payment_summary && $payment_summary['id_payment_statement']) {
                $this->context->smarty->assign(array(
                    'statement' => $payment_summary['statement']
                ));
            }
        }
        $this->setTemplate('payment_result.tpl');
    }
}