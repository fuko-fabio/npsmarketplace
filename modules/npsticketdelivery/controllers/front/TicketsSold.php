<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
require_once _PS_MODULE_DIR_.'npsticketdelivery/classes/HTMLTemplateEventParticipants.php';

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

    public function postProcess() {
        if (Tools::isSubmit('action') && Tools::isSubmit('name')) {
            if (Tools::getValue('action') == 'export') {
                $this->exportToPdf(Tools::getValue('name'), Tools::getValue('date'));
            }
        }
    }

    public function setMedia() {
        parent::setMedia();
        $this->addJS(_PS_MODULE_DIR_.'npsticketdelivery/js/tickets_list.js');
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
            'is_seller' => false,
            'export_events_list' => $this->getEventsToExport()
        ));

        $this->setTemplate('tickets_sold.tpl');
    }

    function getEventsToExport() {
        $result = array();
        $dbquery = new DbQuery();
        $dbquery->select('name')
            ->from('ticket')
            ->where('`id_seller`='.$this->seller->id)
            ->orderBy('name ASC')
            ->groupBy('name');

        $list = Db::getInstance()->executeS($dbquery);
        foreach ($list as $key => $value) {
            $dbquery = new DbQuery();
            $dbquery->select('date')
                ->from('ticket')
                ->where('`name`=\''.$value['name'].'\' AND `id_seller`='.$this->seller->id)
                ->orderBy('date ASC')
                ->groupBy('date');
                $terms = array();
                foreach (Db::getInstance()->executeS($dbquery) as $key2 => $term) {
                    $terms[] = $term['date'];
                }
                $result[] = array('name' => $value['name'], 'terms' => $terms);
        }
        return $result;
    }

    function exportToPdf($name, $date) {
        if ($name) {
            $dbquery = new DbQuery();
            $dbquery->select('*')
                ->from('ticket')
                ->orderBy('person ASC')
                ->where('`id_seller`='.$this->seller->id.' AND name=\''.$name.'\' '.($date != '0' ? 'AND date=\''.$date.'\'' : ''));
            $pdf = new PDF(array(array(
                'participants' => $this->module->fillTickets(Db::getInstance()->executeS($dbquery)),
                'name' => $name,
                'date' => $date,
                'currency' => $this->context->currency
            )), 'EventParticipants', $this->context->smarty);
            $pdf->render();
        }
    }
}
