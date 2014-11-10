<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class HTMLTemplateTicket extends HTMLTemplate {

    protected $ticket_data;
    public $available_in_your_account = false;
    protected $id_ticket;
        
    public function __construct($ticket_data, $smarty) {
        $this->smarty = $smarty;
        $this->ticket_data = $ticket_data;
        $this->id_ticket = 'XXX12';

        $ctx = Context::getContext();
        $id_lang = $ctx->language->id;
        $this->initTitle();
        // footer informations
        $this->shop = $ctx->shop;
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        if (Tools::getValue('debug'))
            die(json_encode($this->ticket_data));
        $this->smarty->assign($this->ticket_data);
        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsticketdelivery/views/templates/pdf/normal_ticket.tpl');
    }

    public function getBulkFilename() {
        return 'tickets.pdf';
    }

    public function getFilename() {
        return $this->id_ticket.'.pdf';
    }
    
    public function initTitle() {
        return '';
    }

    public function getHeader() {
        return '';
    }

    public function getFooter() {
        return '';
    }

    protected function getLogo() {
        $logo = '';

        $physical_uri = Context::getContext()->shop->physical_uri.'img/';

        if (Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id)))
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id);
        elseif (Configuration::get('PS_LOGO', null, null, $this->shop->id) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $this->shop->id)))
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $this->shop->id);
        return '';
    }
}

