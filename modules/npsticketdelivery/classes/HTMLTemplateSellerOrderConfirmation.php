<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class HTMLTemplateSellerOrderConfirmation extends HTMLTemplate {

    public $order;
    public $available_in_your_account = false;

    public function __construct(OrderInvoice $order_invoice, $smarty) {
        $this->order_invoice = $order_invoice;
        $this->order = new Order((int)$this->order_invoice->id_order);
        $this->smarty = $smarty;

        // header informations
        $this->date = Tools::displayDate($this->order->date_add);

        $id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateSellerOrderConfirmation::l('Invoice ').' #'.Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, (int)$this->order->id_shop).sprintf('%06d', $this->order->id);
        // footer informations
        $this->shop = new Shop((int)$this->order->id_shop);
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent() {
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
            'items' => $this->getSellersProducts($this->order),
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
        return $this->smarty->fetch(_PS_MODULE_DIR_.'npsticketdelivery/views/templates/pdf/invoice.tpl');
    }

    /**
     * Returns the tax tab content
     */
    public function getTaxTabContent() {
        $debug = Tools::getValue('debug');

        $address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
                            && !empty($address->vat_number)
                            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
        $carrier = new Carrier($this->order->id_carrier);
            
        $data = array(
            'tax_exempt' => $tax_exempt,
            'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
            'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
            'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
            'order' => $debug ? null : $this->order,
            'order_invoice' => $debug ? null : $this->order_invoice,
            'carrier' => $debug ? null : $carrier
        );

        if ($debug)
            return $data;

        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
    }

    private function getSellersProducts(Order $order) {
        $info_seller = array();
        foreach ($order->getProducts() as $product) {
            $id_seller = Seller::getSellerByProduct($product['id_product']);
            if (!$id_seller) {
                $msg = 'Unable to find owner of product '.$product['name'].'with ID: '.$product['id_product'].' Ticket cannot be generated';
                syslog(LOG_WARNING, $msg);
                PrestaShopLogger::addLog($msg);
                continue;
            }
            $seller = new Seller($id_seller);
            $seller_address = new Address($seller->id_address);
            $seller_address_block_html = AddressFormat::generateAddress($seller_address, array('avoid' => array()), '<br />', ' ', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname'  => '<span style="font-weight:bold;">%s</span>'
            ));
            if (isset($info_seller[$seller->id])) {
                $info_seller[$seller->id]['order_details'][] = $product;
            } else {
                $info_seller[$seller->id] = array(
                    'customer' => new Customer($seller->id_customer),
                    'seller' => $seller,
                    'address_html' => $seller_address_block_html,
                    'order_details' => array($product)
                );
            }
        }
        return $info_seller;
    }

    /**
     * Returns the template filename when using bulk rendering
     * @return string filename
     */
    public function getBulkFilename() {
        return 'invoices.pdf';
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename() {
        return Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order->id).'.pdf';
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