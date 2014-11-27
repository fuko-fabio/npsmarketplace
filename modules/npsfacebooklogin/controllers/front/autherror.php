<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsFacebookLoginautherrorModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $this->context->smarty->assign(array(
            'error' => Tools::getValue('error') == 'permisions' ? 1 : 0,
        ));

        $this->setTemplate('autherror.tpl');
    }
}
?>