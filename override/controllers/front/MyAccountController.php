<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/TicketsGenerator.php');

class MyAccountController extends MyAccountControllerCore {

    public function initContent() {
        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'myTickets' => $this->getTickets()
        ));
        parent::initContent();
    }

    private function getTickets() {
        $nbTickets = CartTicket::getCustomerTickets($this->context->customer->id, 0, 0, true);
        $this->pagination($nbTickets);

        $tickets = CartTicket::getCustomerTickets($this->context->customer->id, $this->p, $this->n);
        foreach ($tickets as $key => $value) {
            $tickets[$key]['code'] = TicketsGenerator::getCode($value);
            $tickets[$key]['seller'] = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'seller WHERE id_seller='.$value['id_seller']);
            $tickets[$key]['seller_shop'] = $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $value['id_seller']));
        }
        return $tickets;
    }
}

