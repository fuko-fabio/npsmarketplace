<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class HTMLTemplateShopSalesReport extends HTMLTemplate {

    protected $report_data;
    protected $month_summary = false;
    
    public $available_in_your_account = false;
        
    public function __construct($report, $smarty) {
        $this->smarty = $smarty;
        $this->report_data = $report['report_data'];
        if (isset($report['month_summary'])) {
            $this->month_summary = $report['month_summary'];
        }
        // header informations
        $this->date = Tools::displayDate(date('Y-m-d'));

        $ctx = Context::getContext();
        $id_lang = $ctx->language->id;
        $this->initTitle();
        // footer informations
        $this->shop = $ctx->shop;
    }

    protected function initTitle() {
        $this->title = HTMLTemplateShopSalesReport::l('Report').
            ' '.HTMLTemplateShopSalesReport::l('from').': '.$this->report_data[0]['start_date'].
            ' '.HTMLTemplateShopSalesReport::l('to').': '.$this->report_data[0]['end_date'];
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        $items = $this->getItems();
		$data = array(
		    'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
            'date' => $this->date,
            'items' => $items,
            'total_commission' => $this->count($items, 'total_commission'),
            'total_seller' => $this->count($items, 'total_seller'),
            'total' => $this->count($items, 'total'),
            'start_date' => $this->report_data[0]['start_date'],
			'end_date' => $this->report_data[0]['end_date'],
		);

		if (Tools::getValue('debug'))
			die(json_encode($data));

		$this->smarty->assign($data);

        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsprzelewy24/views/templates/pdf/shop-report.tpl');
    }

    private function getItems() {
      $result = array();
      foreach ($this->report_data as $data) {
          if($data['empty'])
              continue;
          $result[] = array(
              'total' => $data['total'],
              'total_commission' => $data['total_commison'],
              'total_seller' => $data['total_seller'],
              'id_seller' => $data['id_seller'],
              'company_name' => $data['company_name']
          );
      }
      return $result;
    }

    private function count($items, $attr) {
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item[$attr];
        }
        return $sum;
    }

    /**
     * Returns the template filename when using bulk rendering
     * @return string filename
     */
    public function getBulkFilename() {
        return 'shop_reports.pdf';
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename() {
        $path = '';
        if ($this->month_summary) {
            $path = _PS_ROOT_DIR_._NPS_REPORTS_DIR_;
            @mkdir(_PS_ROOT_DIR_._NPS_REPORTS_DIR_);
        }
        $file = $path.$this->report_data[0]['start_date'].'_to_'.$this->report_data[0]['end_date'].'.pdf';
        return $file;
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

