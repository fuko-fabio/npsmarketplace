<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/Ticket.php');

class TicketsGenerator {
    
    public static function generateAndSend($id_cart_ticket) {
        $c_t = new CartTicket($id_cart_ticket);
        $tickets = CartTicket::getAllTickets($id_cart_ticket);
        $attachments = array();
        foreach ($tickets as $ticket) {
            $t = TicketsGenerator::generateTicket($ticket);
            $attachments[] = array(
                'content' => $t['content'],
                'name' => $t['code'].'.pdf',
                'mime' => 'application/pdf'
            );
        }
        TicketsGenerator::sentTickets($c_t->email, $attachments);
    }

    public static function generateTicket($ticket) {
        require_once(_PS_TCPDF_PATH_.'/barcodes.php');
        require_once(_PS_TOOL_DIR_.'dompdf/dompdf_config.inc.php');
        $code = TicketsGenerator::getCode($ticket);
        $barcodeobj = new TCPDFBarcode($code, 'C128');
        $ticket['barcode'] = $barcodeobj->getBarcodeHTML(1, 90);
        $ticket['code'] = $code;
        $smarty =  Context::getContext()->smarty;
        $smarty->assign($ticket);
        $html = $smarty->fetch(_PS_MODULE_DIR_.'npsticketdelivery/views/templates/pdf/normal_ticket.tpl');
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        return array(
            'content' => $dompdf->output(),
            'code' => $code
        );
    }

    public static function sentTickets($email, $attachments) {
        $mail_params = array(
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
        );
        Mail::Send(Context::getContext()->language->id,
            'tickets',
            Mail::l('Tickets from LabsInTown'),
            $mail_params,
            $email,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            $attachments,
            null,
            _PS_MODULE_DIR_.'npsticketdelivery/mails/');
    }
    
    public static function getCode($ticket) {
        $code = (int)$ticket['id_seller'] * 1000000;
        $code = $code + (int)$ticket['id_ticket'];
        $code = $code + 1000000000000;
        return number_format($code, 0, '', ' ');
    }
}