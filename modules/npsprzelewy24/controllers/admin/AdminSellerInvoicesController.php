<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/SellerInvoice.php');

class AdminSellerInvoicesController extends AdminController {

    protected $_defaultOrderBy = 'generated_date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'seller_invoice';
        $this->className = 'SellerInvoice';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        $this->fields_list = array(
            'id_seller_invoice' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'id_seller' => array(
                'title' => $this->l('Seller ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'start_date' => array(
                'title' => $this->l('From Date'),
                    'type' => 'date',
                    'align' => 'text-right'
            ),
            'end_date' => array(
                'title' => $this->l('To Date'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'generated_date' => array(
                'title' => $this->l('Generated on'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'filename' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }
}