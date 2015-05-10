<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*/

class HTMLTemplateEventParticipants extends HTMLTemplate {

    private $ticket;
    public $available_in_your_account = false;

    public function __construct($ticket, $smarty) {
        $this->ticket = $ticket;
        $this->smarty = $smarty;
        $this->date = Tools::displayDate(date("Y-m-d H:i:s"));
        $id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateEventParticipants::l('Event participants');
        $this->shop = new Shop(Shop::getCurrentShop());
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        if (Tools::getValue('debug'))
            die(json_encode($this->ticket));

        $this->smarty->assign($this->ticket);
        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsticketdelivery/views/templates/pdf/participants.tpl');
    }

    /**
     * Returns the template filename when using bulk rendering
     * @return string filename
     */
    public function getBulkFilename() {
        return 'participants.pdf';
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename() {
        return $this->convertToFilename($this->ticket['name']).'.pdf';
    }

    function convertToFilename ($string) {
        
        // Replace spaces with underscores and makes the string lowercase
        $string = str_replace (" ", "_", $string);
        $string = str_replace ("..", ".", $string);
        $string = strtolower ($string);
        
        // Match any character that is not in our whitelist
        preg_match_all ("/[^0-9^a-z^_^.]/", $string, $matches);
        
        // Loop through the matches with foreach
        foreach ($matches[0] as $value) {
            $string = str_replace($value, "", $string);
        }
        return $string;
    }

    public function getHeader() {
        $shop_name = Configuration::get('PS_SHOP_NAME', null, null, $this->shop->id);
        $path_logo = $this->getLogo();

        $width = 0;
        $height = 0;
        if (!empty($path_logo))
            list($width, $height) = getimagesize($path_logo);

        $this->smarty->assign(array(
            'logo_path' => $path_logo,
            'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'title' => $this->title,
            'date' => $this->date,
            'shop_name' => $shop_name,
            'width_logo' => $width,
            'height_logo' => $height
        ));

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    public function getFooter() {
        $shop_address = $this->getShopAddress();
        $this->smarty->assign(array(
            'available_in_your_account' => $this->available_in_your_account,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $this->shop->id),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $this->shop->id),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, $this->shop->id),
            'free_text' => null
        ));

        return $this->smarty->fetch($this->getTemplate('footer'));
    }

    protected function getLogo() {
        $logo = '';

        $physical_uri = Context::getContext()->shop->physical_uri.'img/';

        if (Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id)))
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $this->shop->id);
        elseif (Configuration::get('PS_LOGO', null, null, $this->shop->id) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $this->shop->id)))
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $this->shop->id);
        return $logo;
    }
}