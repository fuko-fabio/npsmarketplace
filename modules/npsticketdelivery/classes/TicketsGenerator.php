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
            $attachments[] = TicketsGenerator::generateTicket($ticket);
        }
        TicketsGenerator::sentTickets($c_t->email, $attachments);
    }

    public static function generateTicket($ticket) {
        require_once _PS_MODULE_DIR_.'npsticketdelivery/classes/HTMLTemplateTicket.php';
        $pdf = new PDF(array($ticket), 'Ticket', Context::getContext()->smarty);
        $file_attachement = array();
        $file_attachement['content'] = $pdf->render(false);
        $file_attachement['name'] = $pdf->filename;
        $file_attachement['mime'] = 'application/pdf';
        return $file_attachement;
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
}