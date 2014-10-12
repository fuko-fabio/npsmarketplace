<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class HTMLTemplateSalesReport extends HTMLTemplate {

    private $seller;
    private $start_date;
    private $end_date;

    public function __construct($report, $smarty) {
        $this->smarty = $smarty;
        $this->seller = $report['seller'];
        $this->start_date = $report['start_date'];
        $this->end_date = $report['end_date'];

        // header informations
        $this->date = Tools::displayDate(date('Y-m-d'));

        $id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateSalesReport::l('Sales Report').' / '.$this->seller->name;
        // footer informations
        $this->shop = new Shop((int)$this->order->id_shop);
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
        d($this);
		$country = new Country((int)$this->order->id_address_invoice);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer);

		$data = array(
			'order' => $this->order,
			'order_details' => $this->order_invoice->getProducts(),
			'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
			'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer
		);

		if (Tools::getValue('debug'))
			die(json_encode($data));

		$this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

    /**
     * Returns the invoice template associated to the country iso_code
     * @param string $iso_country
     */
    protected function getTemplateByCountry($iso_country) {
        $file = 'sales-report';

        // try to fetch the iso template
        $template = $this->getTemplate($file.'.'.$iso_country);

        // else use the default one
        if (!$template)
            $template = $this->getTemplate($file);

        return $template;
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

    protected function getTemplate($template_name) {
        return _PS_MODULE_DIR_.'npsprzelewy24/views/templates/pdf/'.$template_name.'.tpl';;
    }

}

