<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once _PS_MODULE_DIR_.'npsticketdelivery/classes/HTMLTemplateSellerOrderConfirmation.php';

class NpsTicketDeliveryInvoiceModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function postProcess() {
        if (Tools::isSubmit('id_order')) {
            $order = new Order(Tools::getValue('id_order'));
            if ($order->id_customer != $this->context->customer->id)
                Tools::redirect($this->context->link->getPageLink('history'));
            $pdf = new PDF($order, 'SellerOrderConfirmation', $this->context->smarty);
            $pdf->render();
        }
    }
}
