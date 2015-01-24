<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/Ticket.php');

class TicketsGenerator {

    public static function generateAndSend($id_cart_ticket, $context = null) {
        $c_t = new CartTicket($id_cart_ticket);
        $tickets = CartTicket::getAllTickets($id_cart_ticket);
        $is_gift = Db::getInstance()->getValue('SELECT `gift` FROM '._DB_PREFIX_.'cart WHERE id_cart='.$c_t->id_cart);
        $attachments = array();
        foreach ($tickets as $ticket) {
            $ticket['gift'] = $is_gift;
            $ticket['seller_name'] = Db::getInstance()->getValue('SELECT `name` FROM '._DB_PREFIX_.'seller WHERE id_seller='.$ticket['id_seller']);
            $t = TicketsGenerator::generateTicket($ticket, $context);
            $attachments[] = array(
                'content' => $t['content'],
                'name' => $t['code'].'.pdf',
                'mime' => 'application/pdf'
            );
        }
        TicketsGenerator::sentTickets($c_t, $attachments, $context);
        TicketsGenerator::updateOrderState($c_t->id_cart);
    }

    public static function generateTicket($ticket, $context = null) {
        require_once(_PS_TCPDF_PATH_.'/barcodes.php');
        require_once(_PS_TOOL_DIR_.'dompdf/dompdf_config.inc.php');
        $code = TicketsGenerator::getCode($ticket);
        $barcodeobj = new TCPDFBarcode($code, 'C128');
        $ticket['barcode'] = $barcodeobj->getBarcodeHTML(1, 90);
        $ticket['code'] = $code;
        if ($context == null) {
            $context = Context::getContext();
        }
        $smarty =  $context->smarty;
        $smarty->assign($ticket);
        $html = $smarty->fetch(_PS_MODULE_DIR_.'npsticketdelivery/views/templates/pdf/ticket.tpl');
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        return array(
            'content' => $dompdf->output(),
            'code' => $code
        );
    }

    public static function sentTickets($cart_ticket, $attachments, $context = null) {
        $gift_msg = Db::getInstance()->executeS('SELECT gift_message FROM '._DB_PREFIX_.'cart WHERE id_cart='.$cart_ticket->id_cart);
        $mail_params = array(
            '{gift_message}' => $gift_msg,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
        );
        if ($context == null) {
            $context = Context::getContext();
        }
        Mail::Send($context->language->id,
            'tickets',
            Mail::l('Tickets'),
            $mail_params,
            explode(',', $cart_ticket->email),
            null,
            null,
            null,
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

    public static function updateOrderState($id_cart) {
        $order_id = Order::getOrderByCartId(intval($id_cart));
        $order = new Order($order_id);

        $history = new OrderHistory();
        $history->id_order = intval($order_id);
        $history->changeIdOrderState(4, intval($order_id), true);
        $history->add();
        
        $history = new OrderHistory();
        $history->id_order = intval($order_id);
        $history->changeIdOrderState(5, intval($order_id), true);
        $history->add();
    }
}