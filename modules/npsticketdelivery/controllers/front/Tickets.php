<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/Ticket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/TicketsGenerator.php');

class NpsTicketDeliveryTicketsModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function postProcess() {
        if (Tools::isSubmit('id_ticket')) {
            $ticket = Ticket::getForCustomer($this->context->customer->id, Tools::getValue('id_ticket'));
            if(isset($ticket)) {
                $g_t = TicketsGenerator::generateTicket($ticket);
                header('Content-Description: File Transfer');
                header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
                header('Pragma: public');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                // force download dialog
                if (strpos(php_sapi_name(), 'cgi') === false) {
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/octet-stream', false);
                    header('Content-Type: application/download', false);
                    header('Content-Type: application/pdf', false);
                } else {
                    header('Content-Type: application/pdf');
                }
                // use the Content-Disposition header to supply a recommended filename
                header('Content-Disposition: attachment; filename="'.$g_t['code'].'.pdf"');
                header('Content-Transfer-Encoding: binary');
                echo $g_t['content'];
            }
        }
    }
}
