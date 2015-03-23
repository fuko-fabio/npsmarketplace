<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsPrzelewy24PaymentCancelModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        if(Tools::getValue('error')) {
            $this->errors[] = $this->module->l('Unable to register transaction in Przelewy24 service. Please contact with customer support', 'paymentCancel');
        }
        $this->setTemplate('payment_cancel.tpl');
    }
}