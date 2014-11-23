<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class AuthController extends AuthControllerCore {

    public function initContent() {
        $this->context->smarty->assign(array(
            'HOOK_DISPLAY_LOGIN_SOURCE' => Hook::exec('displayLoginSource'),
        ));
        parent::initContent();
    }

}
