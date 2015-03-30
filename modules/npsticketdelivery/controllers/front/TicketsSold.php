<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsTicketDeliveryTicketsSoldModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;
    private $seller;

    public function init() {
        $this->page_name = 'tickets-sold';
        parent::init();
        $this->seller = new Seller(null, $this->context->customer->id);
        if ($this->seller->id == null) 
            Tools::redirect('index.php?controller=my-account');
    }

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
        $this->addJqueryPlugin('scrollTo');
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $nbTickets = CartTicket::getAllTicketsBySellerId($this->seller->id, 0, 0, true);
        $this->pagination($nbTickets);

        $tickets = CartTicket::getAllTicketsBySellerId($this->seller->id, $this->p, $this->n);
        foreach ($tickets as $key => $ticket) {
            $tickets[$key]['order_url'] = $this->context->link->getModuleLink(
                'npsmarketplace', 'OrderView', array(
                    'id_order' => Order::getOrderByCartId($ticket['id_cart']),
                    'id_seller' => $this->seller->id
                ));
        }
        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'tickets' => $this->module->fillTickets($tickets),
            'is_seller' => false
        ));

        $this->setTemplate('tickets_sold.tpl');
    }
}
