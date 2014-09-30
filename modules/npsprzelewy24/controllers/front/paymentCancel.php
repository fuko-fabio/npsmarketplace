<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsPrzelewy24PaymentCancelModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function init() {
        parent::init();
        $this->setTemplate('payment_cancel.tpl');
    }
}