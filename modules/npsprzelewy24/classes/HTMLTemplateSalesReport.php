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
    public $available_in_your_account = false;

    public function __construct($report, $smarty) {
        $this->smarty = $smarty;
        $this->seller = new Seller($report['id_seller']);
        $this->start_date = $report['start_date'];
        $this->end_date = $report['end_date'];

        // header informations
        $this->date = Tools::displayDate(date('Y-m-d'));

        $id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateSalesReport::l('Sales report').' '.HTMLTemplateSalesReport::l('from').': '.$this->start_date.' '.HTMLTemplateSalesReport::l('to').': '.$this->end_date;
        // footer informations
        $this->shop = new Shop((int)$this->order->id_shop);
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
		    'id_currency' => 1,
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
        return array(array(
            'product_name' => 'Test Name',
            'product_reference' => 'TESTREF01',
            'unit_price' => 120.50,
            'product_quantity' => 2,
            'total_price' => 241,
            'commision_price' => 15.50,
            'seller_price' => 225.50
        ),
        array(
            'product_name' => 'Test Next Name',
            'product_reference' => 'TESTREF02',
            'unit_price' => 20,
            'product_quantity' => 1,
            'total_price' => 20,
            'commision_price' => 1.20,
            'seller_price' => 18.80
        ));
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
        return $this->seller->name.'_'.$this->start_date.'_to_'.$this->end_date.'.pdf';
    }

}

