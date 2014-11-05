<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class ProductController extends ProductControllerCore {

    public function initContent() {
        $this->context->smarty->assign(array(
            'HOOK_EXTRA_PRODUCT_IMAGE' => Hook::exec('displayExtraProductImage'),
        ));
        parent::initContent();
    }
}
