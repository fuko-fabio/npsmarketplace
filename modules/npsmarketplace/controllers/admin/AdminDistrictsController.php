<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/District.php');

class AdminDistrictsController extends AdminController
{
    protected $delete_mode;

    protected $_defaultOrderBy = 'name';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'district';
        $this->className = 'District';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->allow_import = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
        ));

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        $this->fields_list = array(
            'id_district' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            )
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }
    public function initToolbar()
    {
        if (empty($this->display))
        {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=categories',
                'desc' => $this->l('Import')
            );
        }
        parent::initToolbar();
    }

    public function initToolbarTitle() {
        parent::initToolbarTitle();

        switch ($this->display)
        {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage Marketplace Districts');
                break;
            case 'edit':
                if (($district = $this->loadObject(true)) && Validate::isLoadedObject($district))
                    $this->toolbar_title[] = sprintf($this->l('Editing District: %s'), Tools::substr($district->name, 0, 1));
                break;
        }
    }

    public function renderForm() {
        if (!($obj = $this->loadObject(true)))
            return;
        $obj = $this->loadObject(true);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('District'),
                'icon' => 'icon-building'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true
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
}