<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
require_once _PS_MODULE_DIR_.'npsticketdelivery/classes/HTMLTemplateEventParticipants.php';
require_once(_PS_TOOL_DIR_.'phpexcel/PHPExcel.php');

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
            $action = Tools::getValue('action');
            $name = Tools::getValue('name');
            $date = Tools::getValue('date');
            $filetype = Tools::getValue('filetype');
            $participants = $this->getParticipantsList($name, $date, Tools::getIsset('questions'));

            if ($action == 'export') {
                if ($filetype == 'pdf') {
                    $this->exportToPdf($participants, $name, $date);
                } elseif ($filetype == 'excel') {
                    $this->exportToExcel($participants, $name, $date);
                }
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
            'tickets' => $this->module->fillTickets($tickets, true),
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

    function exportToPdf($participants, $name, $date) {
        $pdf = new PDF(array(array(
            'participants' => $participants,
            'name' => $name,
            'date' => $date,
            'currency' => $this->context->currency
        )), 'EventParticipants', $this->context->smarty);
        $pdf->pdf_renderer->setFontSubsetting(false);
        $pdf->render();
    }

    static $KEY_CELLS = array(
        'A' => 'person',
        'B' => 'combination_name',
        'C' => 'price',
        'D' => 'code',
        'E' => 'date',
        'F' => 'email'
    );

    function exportToExcel($participants, $name, $date) {
        $fullName = $name." - ";
        if ($date == 0) {
            $fullName = $fullName.$this->module->l('All terms', 'TicketsSold');
        } else {
            $fullName = $fullName.date_format(date_create($date), 'Y-m-d H:i');
        }

        #PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator($this->module->l('Labsintown', 'TicketsSold'))
            ->setLastModifiedBy($this->module->l('Labsintown', 'TicketsSold'))
            ->setTitle($fullName)
            ->setSubject($fullName)
            ->setDescription($this->module->l('List of event participants.', 'TicketsSold'))
            ->setKeywords("labsintown participants")
            ->setCategory("labsintown");
            
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        foreach($sheet->getRowDimensions() as $rd) { 
            $rd->setRowHeight(-1); 
        }
        foreach(range('A','G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $headerCells = array(
            'A' => $this->module->l('Person', 'TicketsSold'),
            'B' => $this->module->l('Type', 'TicketsSold'),
            'C' => $this->module->l('Price', 'TicketsSold'),
            'D' => $this->module->l('Ticket', 'TicketsSold'),
            'E' => $this->module->l('Date', 'TicketsSold'),
            'F' => $this->module->l('Email(buyer)', 'TicketsSold')
        );
        foreach ($headerCells as $key => $value) {
            $sheet->setCellValue($key.'1', $value);
        }
        $index = 2;
        foreach ($participants as $participant) {
            foreach (self::$KEY_CELLS as $key => $value) {
                if ($value == 'price') {
                    $v = round($participant[$value], 2);
                } elseif ($value == 'date') {
                    if($participant[$value] == '0000-00-00 00:00:00') {
                        $v = $this->module->l('Carnet', 'TicketsSold');
                    } else {
                        $v = date_format(date_create($participant[$value]), 'Y-m-d H:i');
                    }
                } else {
                    $v = $participant[$value];
                }
                $sheet->setCellValue($key.$index, $v);
            }
            if (isset($participant['questions']) && !empty($participant['questions'])) {
                $celVal = "";
                foreach ($participant['questions'] as $item) {
                    if (isset($item['question']) && $item['answer']) {
                        $celVal = $celVal.$item['question']."  ".$item['answer']."\n";
                    }
                }
                $sheet->setCellValue("G".$index, $celVal);
                $sheet->getStyle("G".$index)->getAlignment()->setWrapText(true);
            }
            $index++;
        }
        
        $sheet->getStyle('A1:F1')->applyFromArray(array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFFFAA00')),
            'font'  => array(
                'bold'  => true,
                'size'  => 12)
        ));
        
        $sheet->getStyle('A2:G1000')->applyFromArray(array(
            'font'  => array(
                'bold'  => false,
                'size'  => 12)
        ));

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fullName.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    function getParticipantsList($name, $date, $questions) {
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('ticket', 't')
            ->orderBy('person ASC')
            ->where('`id_seller`='.$this->seller->id.' AND name=\''.$name.'\' '.($date != '0' ? 'AND date=\''.$date.'\'' : ''))
            ->leftJoin('cart_ticket', 'ct', 't.id_cart_ticket = ct.id_cart_ticket');

        return $this->module->fillTickets(Db::getInstance()->executeS($dbquery), $questions);
    }

}
