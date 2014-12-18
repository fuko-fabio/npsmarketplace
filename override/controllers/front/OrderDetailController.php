<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class OrderDetailController extends OrderDetailControllerCore {

    public function initContent() {
        $this->context->smarty->assign(array(
            'p24_awaiting_id' => Configuration::get('NPS_P24_ORDER_STATE_AWAITING'),
            'p24_accepted_id' => Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED'),
        ));
        parent::initContent();
    }
}
