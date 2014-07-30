<?php

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class AdminSellersAccountsController extends AdminController
{
	protected $delete_mode;

	protected $_defaultOrderBy = 'request_date';
	protected $_defaultOrderWay = 'DESC';

	public function __construct()
	{
		$this->bootstrap = true;
		$this->required_database = true;
		$this->table = 'seller';
		$this->className = 'Seller';
		$this->lang = true;
		$this->explicitSelect = true;

		$this->allow_export = true;
        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'seller'
        );

		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->context = Context::getContext();
		$this->default_form_language = $this->context->language->id;
		
		$this->fields_list = array(
			'id_seller' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'company_name' => array(
                'title' => $this->l('Company Name')
            ),
            'nip' => array(
                'title' => $this->l('NIP')
            ),
            'regon' => array(
                'title' => $this->l('REGON')
            ),
            'email' => array(
                'title' => $this->l('Email address')
            ),
            'locked' => array(
                'title' => $this->l('Locked'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printLockedIcon',
                'orderby' => false
            ),
            'request_date' => array(
            'title' => $this->l('Registration'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active'
            ),
            'commision' => array(
                'title' => $this->l('Commision'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
		);

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_CUSTOMER;

		parent::__construct();
	}

	public function printLockedIcon($value, $seller)
    {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminSellersAccounts&id_seller='
            .(int)$seller['id_seller'].'&changeLockedVal&token='.Tools::getAdminTokenLite('AdminSellersAccounts').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function processChangeLockedVal()
    {
        $seller = new Seller($this->id_object);
        if (!Validate::isLoadedObject($seller))
            $this->errors[] = Tools::displayError('An error occurred while updating seller information.');
        $seller->locked = $seller->locked ? 0 : 1;
        if (!$seller->update())
            $this->errors[] = Tools::displayError('An error occurred while updating seller information.');
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        
        //TODO Activate/Deactivate products
    }

	public function initContent()
	{
		if ($this->action == 'select_delete')
			$this->context->smarty->assign(array(
				'delete_form' => true,
				'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
				'boxes' => $this->boxes,
			));

		parent::initContent();
	}

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        switch ($this->display)
        {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage your Sellers');
                break;
            case 'edit':
                if (($seller = $this->loadObject(true)) && Validate::isLoadedObject($seller))
                    $this->toolbar_title[] = sprintf($this->l('Editing Seller: %s'), Tools::substr($seller->name, 0, 1));
                break;
        }
    }

	public function initProcess()
	{
		parent::initProcess();

		if (Tools::isSubmit('changeLockedVal') && $this->id_object)
        {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_locked_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }

		// When deleting, first display a form to select the type of deletion
		if ($this->action == 'delete' || $this->action == 'bulkdelete')
			if (Tools::getValue('deleteMode') == 'real' || Tools::getValue('deleteMode') == 'deleted')
				$this->delete_mode = Tools::getValue('deleteMode');
			else
				$this->action = 'select_delete';
	}

	public function renderList()
	{
		if (Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table))
			$this->tpl_list_vars = array(
				'delete_seller' => true,
				'REQUEST_URI' => $_SERVER['REQUEST_URI'],
				'POST' => $_POST
			);

		return parent::renderList();
	}

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true)))
            return;
        $obj = $this->loadObject(true);
        
        $image = _NPS_SEL_IMG_DIR_.$obj->id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
            $this->imageType, true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Seller'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Commision'),
                    'name' => 'commision',
                    'class' => 'fixed-width-xs',
                    'suffix' => '%',
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'hint' => $this->l('Change seller account status'),
                    'desc' => $this->l('Activate/Deactivate seller account. This will have impact on seller products and his profile visibility'),
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
                    'label' => $this->l('Locked'),
                    'name' => 'locked',
                    'hint' => $this->l('Change seller account lock status'),
                    'desc' => $this->l('Lock/Unlock seller account. This will have impact on seller products'),
                    'values' => array(
                        array(
                            'id' => 'locked_on',
                            'value' => 1,
                            'label' => $this->l('Unlocked')
                        ),
                        array(
                            'id' => 'locked_off',
                            'value' => 0,
                            'label' => $this->l('Locked')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Company Name'),
                    'name' => 'company_name',
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Company Description'),
                    'name' => 'company_description',
                    'required' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Seller Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone'),
                    'name' => 'phone',
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
                    'required' => true,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Logo'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'delete_url' => self::$currentIndex.'&'.$this->identifier.'='.$obj->id.'&token='.$this->token.'&deleteImage=1',
                    'hint' => $this->l('Upload a company logo from your computer.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true
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

    protected function postImage($id)
    {
        $ret = parent::postImage($id);
        if (($id_seller = (int)Tools::getValue('id_seller')) &&
            isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_NPS_SEL_IMG_DIR_.$id_seller.'.jpg'))
        {
            $images_types = ImageType::getImagesTypes('sellers');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _NPS_SEL_IMG_DIR_.$id_seller.'.jpg',
                    _NPS_SEL_IMG_DIR_.$id_seller.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }

        return $ret;
    }

    protected function afterDelete($object, $old_id)
    {
        //TODO Delete products
        return true;
    }
    
    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        /* The data generation is located in AdminStatsControllerCore */
        $helper = new HelperKpi();
        $helper->id = 'box-registered-accounts';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->href = $this->context->link->getAdminLink('AdminSellers');
        $helper->title = $this->l('Registered Accounts', null, null, false);
        if (ConfigurationKPI::get('REGISTERED_SELLER_ACCOUNTS') !== false)
            $helper->value = ConfigurationKPI::get('REGISTERED_SELLER_ACCOUNTS');
        if (ConfigurationKPI::get('REGISTERED_SELLER_ACCOUNTS_EXPIRE') < $time)
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=registered_sellers';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-active-accounts';
        $helper->icon = 'icon-off';
        $helper->color = 'color4';
        $helper->href = $this->context->link->getAdminLink('AdminSellers');
        $helper->title = $this->l('Active Accounts', null, null, false);
        if (ConfigurationKPI::get('ACTIVE_SELLER_ACCOUNTS') !== false)
            $helper->value = ConfigurationKPI::get('ACTIVE_SELLER_ACCOUNTS');
        if (ConfigurationKPI::get('ACTIVE_SELLER_ACCOUNTS_EXPIRE') < $time)
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=active_sellers';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-disabled-accounts';
        $helper->icon = 'icon-off';
        $helper->color = 'color2';
        $helper->href = $this->context->link->getAdminLink('AdminSellers');
        $helper->title = $this->l('Disabled Accounts', null, null, false);
        if (ConfigurationKPI::get('DISABLED_SELLER_ACCOUNTS') !== false)
            $helper->value = ConfigurationKPI::get('DISABLED_SELLER_ACCOUNTS');
        if (ConfigurationKPI::get('DISABLED_SELLER_ACCOUNTS_EXPIRE') < $time)
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_sellers';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-locked-accounts';
        $helper->icon = 'icon-lock';
        $helper->color = 'color3';
        $helper->href = $this->context->link->getAdminLink('AdminSellers');
        $helper->title = $this->l('Locked Accounts', null, null, false);
        if (ConfigurationKPI::get('LOCKED_SELLER_ACCOUNTS') !== false)
            $helper->value = ConfigurationKPI::get('LOCKED_SELLER_ACCOUNTS');
        if (ConfigurationKPI::get('LOCKED_SELLER_ACCOUNTS_EXPIRE') < $time)
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=locked_sellers';
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }
}