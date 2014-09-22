<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_NPS_MAILS_DIR_' ) )
    define('_NPS_MAILS_DIR_', _PS_MODULE_DIR_.'npsmarketplace/mails/');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class AdminSellersAccountsController extends AdminController
{
    protected $delete_mode;

    protected $_defaultOrderBy = 'request_date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'seller';
        $this->className = 'Seller';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'seller' );
        $this->addRowAction('edit');

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
        $seller_products = $seller->getProducts();
        $payment_config = new P24SellerCompany(null, $seller->id);
        if ($payment_config->id != null && !$seller->locked)
            $this->changeProductsState($seller_products, true);
        else
            $this->changeProductsState($seller_products, false);

        $customer = new Customer($seller->id_customer);
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
            '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
        );
        if ($seller->locked) {
            $template = 'account_locked';
            $title = Mail::l('Your account has been locked');
        } else {
            $template = 'account_unlocked';
            $title = Mail::l('Your account has been unlocked');
        }
        Mail::Send($this->context->language->id,
            $template,
            $title,
            $mail_params,
            $seller->email,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processStatus()
    {
        parent::processStatus();
        $seller = new Seller($this->id_object);
        if (!Validate::isLoadedObject($seller))
            $this->errors[] = Tools::displayError('An error occurred while updating seller information.');

        $seller_products = $seller->getProducts();
        if ($seller->active) {
            $payment_config = new P24SellerCompany(null, $seller->id);
            if ($payment_config->id != null)
                $this->changeProductsState($seller_products, true);

            $customer = new Customer($seller->id_customer);
            $mail_params = array(
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
                '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
                '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
                '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
                '{payment_settings_guide_url}' => Configuration::get('NPS_PAYMENT_SETTINGS_GUIDE_URL'),
                '{seller_payment_configuration_url}' => $this->context->link->getModuleLink('npsprzelewy24', 'PaymentSettings'),
            );
            Mail::Send($this->context->language->id,
                'account_active',
                Mail::l('Seller account activated'),
                $mail_params,
                $seller->email,
                null,
                strval(Configuration::get('PS_SHOP_EMAIL')),
                strval(Configuration::get('PS_SHOP_NAME')),
                null,
                null,
                _NPS_MAILS_DIR_);
        } else {
            $this->changeProductsState($seller_products, false);
        }
    }

    private function changeProductsState($products, $state) {
        foreach ($products as $product) {
            $product->setFieldsToUpdate(array('active' => true));
            $product->active = $state;
            $product->update(false);
        }
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

    public function initProcess() {
        parent::initProcess();

        if (Tools::isSubmit('changeLockedVal') && $this->id_object)
        {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_locked_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
    }

    public function renderList() {
        if (Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table))
            $this->tpl_list_vars = array(
                'delete_seller' => true,
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                'POST' => $_POST
            );

        return parent::renderList();
    }

    public function renderForm() {
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
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('REGON'),
                    'name' => 'regon',
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
                array(
                    'type' => 'switch',
                    'label' => $this->l('Append Company Regulations'),
                    'name' => 'regulations_active',
                    'values' => array(
                        array(
                            'id' => 'append',
                            'value' => 1,
                            'label' => $this->l('Append')
                        ),
                        array(
                            'id' => 'not_append',
                            'value' => 0,
                            'label' => $this->l('Don\'t Append')
                        )
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Company Regulations'),
                    'name' => 'regulations',
                    'lang' => true,
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

    protected function postImage($id) {
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

    public function renderKpis() {
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