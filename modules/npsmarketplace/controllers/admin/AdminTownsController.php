<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Town.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Province.php');

class AdminTownsController extends AdminController
{
    protected $delete_mode;

    protected $_defaultOrderBy = 'name';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'town';
        $this->className = 'Town';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
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
            'id_town' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active'
            ),
            'selectable' => array(
                'title' => $this->l('Selectable'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printSelectableIcon',
                'orderby' => false
            ),
            'default' => array(
                'title' => $this->l('Default'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printIcon',
                'orderby' => false
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function printSelectableIcon($value, $town) {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminTowns&id_town='
            .(int)$town['id_town'].'&changeSelectableVal&token='.Tools::getAdminTokenLite('AdminTowns').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function printIcon($value, $town) {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminTowns&id_town='
            .(int)$town['id_town'].'&changeDefaultVal&token='.Tools::getAdminTokenLite('AdminTowns').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function processChangeSelectableVal() {
        $town = new Town($this->id_object);
        if (!Validate::isLoadedObject($town))
            $this->errors[] = Tools::displayError('An error occurred while updating town information.');
        $town->selectable = !$town->selectable;
        if (!$town->update())
            $this->errors[] = Tools::displayError('An error occurred while updating town information.');
    }

    public function processChangeDefaultVal() {
        $town = new Town($this->id_object);
        if (!Validate::isLoadedObject($town))
            $this->errors[] = Tools::displayError('An error occurred while updating town information.');
        $town->default = !$town->default;
        if (!$town->update())
            $this->errors[] = Tools::displayError('An error occurred while updating town information.');
    }

    public function initToolbarTitle() {
        parent::initToolbarTitle();

        switch ($this->display)
        {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage Marketplace Towns');
                break;
            case 'edit':
                if (($town = $this->loadObject(true)) && Validate::isLoadedObject($town))
                    $this->toolbar_title[] = sprintf($this->l('Editing Town: %s'), Tools::substr($town->name, 0, 1));
                break;
        }
    }

    public function initProcess() {
        parent::initProcess();

        if (Tools::isSubmit('changeDefaultVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_default_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        } else if (Tools::isSubmit('changeSelectableVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_selectable_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
    }

    public function renderForm() {
        if (!($obj = $this->loadObject(true)))
            return;
        $obj = $this->loadObject(true);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Town'),
                'icon' => 'icon-bank'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Active')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Not Active')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Selectable'),
                    'name' => 'selectable',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Selectable')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Not Selectable')
                        )
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Province'),
                    'name' => 'id_province',
                    'required' => true,
                    'options' => array(
                        'query' => Province::getAll($this->context->language->id),
                        'id' => 'id_province',
                        'name' => 'name'
                    ),
                )
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