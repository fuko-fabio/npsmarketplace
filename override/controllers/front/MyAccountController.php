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
        $tickets = CartTicket::getCustomerTickets($this->context->customer->id);
        foreach ($tickets as $key => $value) {
            $tickets[$key]['code'] = TicketsGenerator::getCode($value);
        }
        return $tickets;
    }
}

