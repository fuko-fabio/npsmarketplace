<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class HTMLTemplateSalesReport extends HTMLTemplate {

    private $seller;
    private $start_date;
    private $end_date;
    private $full_start_date;
    private $full_end_date;
    public $available_in_your_account = false;
    private $month_summary = false;

    public function __construct($report, $smarty) {
        $this->smarty = $smarty;
        $this->seller = new Seller($report['id_seller']);
        $this->full_start_date = $report['start_date'];
        $this->full_end_date = $report['end_date'];
        $parts = explode(' ', $report['start_date']);
        $this->start_date = $parts[0];
        $parts = explode(' ', $report['end_date']);
        $this->end_date = $parts[0];
        if (isset($report['month_summary'])) {
            $this->month_summary = $report['month_summary'];
        }
        // header informations
        $this->date = Tools::displayDate(date('Y-m-d'));

        $ctx = Context::getContext();
        $id_lang = $ctx->language->id;
        $this->title = HTMLTemplateSalesReport::l('Sales report').' '.HTMLTemplateSalesReport::l('from').': '.$this->start_date.' '.HTMLTemplateSalesReport::l('to').': '.$this->end_date;
        // footer informations
        $this->shop = $ctx->shop;
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        $p24_company = new P24SellerCompany(null, $this->seller->id);
        $items = $this->getItems();
        $total_commison = $this->count($items, 'commision_price');
        $total_seller = $this->count($items, 'seller_price');
        $total = $this->count($items, 'total_price');
		$data = array(
		    'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
            'date' => $this->date,
            'company' => $p24_company,
            'items' => $items,
            'total_commission' => $total_commison,
            'total_seller' => $total_seller,
            'total' => $total,
            'start_date' => $this->start_date,
			'end_date' => $this->end_date,
			'seller' => $this->seller
		);

		if (Tools::getValue('debug'))
			die(json_encode($data));

		$this->smarty->assign($data);

        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsprzelewy24/views/templates/pdf/sales-report.tpl');
    }

    private function getItems() {
        $rows = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'seller_invoice_data`
            WHERE (date BETWEEN \''.$this->full_start_date.'\' AND \''.$this->full_end_date.'\') AND `id_seller` = '.(int)$this->seller->id
        );
        $result = array();
        foreach ($rows as $data) {
            $p = new Product($data['id_product']);
            $qty = $data['product_qty'];
            $total = $data['product_total_price'] / 100;
            $commission = $data['commission'] / 100;
            $result[] = array(
                'id_currency' => $data['id_currency'],
                'date' => $data['date'],
                'product_name' => $p->name[(int)Configuration::get('PS_LANG_DEFAULT')],
                'product_reference' => $p->reference,
                'unit_price' => $total / $qty,
                'product_quantity' => $qty,
                'total_price' => $total,
                'commision_price' => $commission,
                'seller_price' => $total - $commission
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
        return 'sales_reports.pdf';
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename() {
        $path = '';
        if ($this->month_summary) {
            $path = _PS_ROOT_DIR_._NPS_SELLER_REPORTS_DIR_;
        }
        $file = $path.$this->seller->company_name.'_'.$this->start_date.'_to_'.$this->end_date.'.pdf';
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

