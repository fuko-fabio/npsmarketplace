<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateShopSalesReport.php');

class HTMLTemplateSellerSalesReport extends HTMLTemplateShopSalesReport {

    private $seller;

    public function __construct($report, $smarty) {
        parent::__construct($report, $smarty);
        $this->seller = $report['seller'];
    }

    protected function initTitle() {
        $this->title = HTMLTemplateSellerSalesReport::l('Report').
            ' '.HTMLTemplateSellerSalesReport::l('from').': '.$this->report_data['start_date'].
            ' '.HTMLTemplateSellerSalesReport::l('to').': '.$this->report_data['end_date'];
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        $p24_company = new P24SellerCompany(null, $this->seller->id);
		$data = array(
		    'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
            'date' => $this->date,
            'company' => $p24_company,
            'items' => $this->report_data['items'],
            'total_commission' => $this->report_data['total_commison'],
            'total_seller' => $this->report_data['total_seller'],
            'total' => $this->report_data['total'],
            'start_date' => $this->report_data['start_date'],
			'end_date' => $this->report_data['end_date'],
			'seller' => $this->seller
		);

		if (Tools::getValue('debug'))
			die(json_encode($data));

		$this->smarty->assign($data);

        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsprzelewy24/views/templates/pdf/sales-report.tpl');
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
            @mkdir(_PS_ROOT_DIR_._NPS_REPORTS_DIR_);
            @mkdir(_PS_ROOT_DIR_._NPS_SELLER_REPORTS_DIR_);
        }
        $file = $path.$this->seller->company_name.'_'.$this->report_data['start_date'].'_to_'.$this->report_data['end_date'].'.pdf';
        return $file;
    }

}

