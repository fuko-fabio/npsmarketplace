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
        // 4 steps to the order
        switch ((int)$this->step) {
            case 1:
                $sql = 'SELECT invoice FROM '._DB_PREFIX_.'cart WHERE id_cart='.$this->context->cart->id;
                $this->context->smarty->assign('attach_invoice', Db::getInstance()->getValue($sql));
            break;
            case 2:
                $c_t = CartTicket::getByCartId($this->context->cart->id);
                $email = null;
                if ($c_t != null)
                    $email = $c_t->email;
                $this->context->smarty->assign('ticket_destination', $email);
            break;
        }
        parent::initContent();
    }

	protected function processCarrier()	{
		parent::processCarrier();
        Hook::exec('actionPostProcessCarrier', array(
            'id_cart' =>$this->context->cart->id,
            'ticket_email' => Tools::getValue('ticket_destination'),
            'ticket_person' => $_POST['ticket_person'],
            'ticket_answer' => $_POST['ticket_answer']
        ));
	}

    public function processAddress() {
        if(Tools::getIsset('attach_invoice'))
             Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'cart  SET invoice=1 WHERE id_cart='.$this->context->cart->id);
        return parent::processAddress();
    }

    protected function _checkFreeOrder() {
        $result = parent::_checkFreeOrder();
        if ($result) {
            // small hack for free orders :(
            $id_order = OrderCore::getOrderByCartId($this->context->cart->id);
            $order_state = OrderHistoryCore::getLastOrderState($id_order);
            $order_history = new OrderHistory();
            $order_history->id_order = $id_order;
            $order_history->id_order_state = $order_state->id;
            Hook::exec('actionOrderHistoryAddAfter', array('order_history' => $order_history));
        }
        return $result;
    }
}
