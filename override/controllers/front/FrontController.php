<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class FrontController extends FrontControllerCore {

    public function initContent() {
        parent::initContent();

        $this->context->smarty->assign(array(
            'HOOK_EXTRA_LOGO' => Hook::exec('extraLogo'),
        ));
    }
}
