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
        $this->allow_export = false;

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'seller` s ON (s.`id_seller` = a.`id_seller`)';
        $this->_select .= 's.`id_seller`, s.`name`, s.`company_name`, s.`nip`';
        
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
            'name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'text-center',
            ),
            'company_name' => array(
                'title' => $this->l('Seller Company Name'),
                'align' => 'text-center',
            ),
            'nip' => array(
                'title' => $this->l('Seller NIP'),
                'align' => 'text-center',
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
            'empty' => array(
                'title' => $this->l('Empty'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printIcon',
            ),
            'filename' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function printIcon($value) {
        return $value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>';
    }

    public function printPDFIcons($filename, $tr) {
        return
        '<span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default" target="_blank" href="'._NPS_SELLER_REPORTS_DIR_.$filename.'"><i class="icon-file-text"></i></a>
            </span>
        </span>';
    }
}