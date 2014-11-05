<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class AddressController extends AddressControllerCore {

    public function initContent() {
        if ($this->context->customer->isLogged()) {
            $this->context->smarty->assign(array(
                'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            ));
        }
        parent::initContent();
    }
}
