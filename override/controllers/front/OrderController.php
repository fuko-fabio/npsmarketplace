<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class OrderController extends OrderControllerCore {

    public function initContent() {
        $this->context->smarty->assign(array(
            'HOOK_BEFOREVIRTUALCARRIER' => Hook::exec('displayBeforeVirtualCarrier'),
        ));
        parent::initContent();
    }

	protected function processCarrier()	{
		parent::processCarrier();
        Hook::exec('actionPostProcessCarrier', array(
            'id_cart' =>$this->context->cart->id,
            'ticket_email' => Tools::getValue('ticket_destination'),
            'gift_ticket' => Tools::getIsset('gift_ticket')
        ));
	}

}
