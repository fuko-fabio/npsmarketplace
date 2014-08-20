<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentValidator.php');

class NpsPrzelewy24PaymentSstateModuleFrontController extends ModuleFrontController {

    public function init(){
        parent::init();

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
    }
}