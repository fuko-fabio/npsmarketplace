<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class AdminSellerCompanyController extends AdminController {
    protected $delete_mode;

    protected $_defaultOrderBy = 'registration_date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'p24_seller_company';
        $this->className = 'P24SellerCompany';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->addRowAction('view');
        $this->deleted = false;
        $this->base_tpl_view = 'seller_company_view.tpl';

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'seller` s ON (s.`id_seller` = a.`id_seller`)';
        $this->_select .= 's.`id_seller`, s.`name`, s.`company_name`, s.`nip`';
        
        $this->fields_list = array(
            'id_p24_seller_company' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
            ),
            'id_seller' => array(
                'title' => $this->l('Seller ID'),
                'align' => 'text-center',
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
            'spid' => array(
                'title' => $this->l('SPID'),
                'align' => 'text-center',
            ),
            'iban' => array(
                'title' => $this->l('Bank Account'),
                'align' => 'text-center',
            ),
            'registration_date' => array(
                'title' => $this->l('Registration Date'),
                'type' => 'date',
                'align' => 'text-right'
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function initToolbarTitle() {
        parent::initToolbarTitle();

        switch ($this->display) {
            case 'list':
                $this->toolbar_title[] = $this->l('Sellers Przelewy24 Payment Settings');
                break;
        }
    }

    public function renderForm() {
        if (!($obj = $this->loadObject(true)))
            return;
        $obj = $this->loadObject(true);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Seller Przelewy24 Account'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('SPID'),
                    'name' => 'spid',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Seller ID'),
                    'name' => 'id_seller',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Company Name'),
                    'name' => 'company_name',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Person'),
                    'name' => 'person',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Street'),
                    'name' => 'street',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Post Code'),
                    'name' => 'post_code',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'email',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('NIP'),
                    'name' => 'nip',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('REGON'),
                    'name' => 'regon',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Bank Account'),
                    'name' => 'iban',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Registration Date'),
                    'name' => 'registration_date',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Activation Link'),
                    'name' => 'register_link',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Acceptance of Przelewy24 Regulations'),
                    'name' => 'acceptance',
                    'values' => array(
                        array(
                            'id' => 'yes',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'no',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submit',
            )
        );

        return parent::renderForm();
    }

    public function renderView() {
        $obj = $this->loadObject(true);

        $this->tpl_view_vars = array(
            'company' => $obj,
            'history' => array() //TODO
        );

        $helper = new HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;
        $helper->base_folder = _PS_MODULE_DIR_.'npsprzelewy24/views/templates/admin/';
        if (!is_null($this->base_tpl_view))
            $helper->base_tpl = $this->base_tpl_view;
        $view = $helper->generateView();

        return $view;
    }


}