<?php

class NpsMailactivAtionInfoModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $customer = new Customer(Tools::getValue('customer'));
        $this->context->smarty->assign(array(
            'email' => $customer->email
        ));
        $this->setTemplate('info.tpl');
    }
}