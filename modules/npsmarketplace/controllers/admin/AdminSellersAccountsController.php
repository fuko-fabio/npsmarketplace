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
    
    protected $general_error;

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
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->base_tpl_view = 'seller_view.tpl';
        $this->general_error = Tools::displayError('An error occurred while updating seller information.');

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
            'krs' => array(
                'title' => $this->l('KRS')
            ),
            'nip' => array(
                'title' => $this->l('NIP')
            ),
            'regon' => array(
                'title' => $this->l('REGON')
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
            'outer_adds' => array(
                'title' => $this->l('External Advertisments'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printOuterAddsIcon',
                'orderby' => false
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function printOuterAddsIcon($value, $seller) {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminSellersAccounts&id_seller='
            .(int)$seller['id_seller'].'&changeOuterAddsVal&token='.Tools::getAdminTokenLite('AdminSellersAccounts').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function printLockedIcon($value, $seller) {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminSellersAccounts&id_seller='
            .(int)$seller['id_seller'].'&changeLockedVal&token='.Tools::getAdminTokenLite('AdminSellersAccounts').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function processChangeOuterAddsVal() {
        if (!($obj = $this->loadObject(true))) {
            $this->errors[] = $this->general_error;
            return;
        }
        $obj->outer_adds = !$obj->outer_adds;
        if(!$obj->update())
            $this->errors[] = $this->general_error;
    }

    public function processChangeLockedVal() {
        if (!($obj = $this->loadObject(true))) {
            $this->errors[] = $this->general_error;
            return;
        }
        $obj->locked = !$obj->locked;
        if(!$obj->update()) {
            $this->errors[] = $this->general_error;
            return;
        }
        $seller_products = $obj->getProducts();
        $payment_config = new P24SellerCompany(null, $obj->id);
        if ($payment_config->id != null && !$obj->locked)
            $this->changeProductsState($seller_products, true);
        else
            $this->changeProductsState($seller_products, false);

        $customer = new Customer($obj->id_customer);
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $obj->id)),
            '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
            '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
        );
        if ($obj->locked) {
            $template = 'account_locked';
            $title = $this->l('Your account has been locked');
        } else {
            $template = 'account_unlocked';
            $title = $this->l('Your account has been unlocked');
        }
        Mail::Send($this->context->language->id,
            $template,
            $title,
            $mail_params,
            $customer->email,
            null,
            null,
            null,
            null,
            null,
            _NPS_MAILS_DIR_);
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processStatus() {
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
                '{seller_shop_name}' => $seller->name,
                '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
                '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
                '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
                '{payment_settings_guide_url}' => Configuration::get('NPS_PAYMENT_SETTINGS_GUIDE_URL'),
                '{seller_payment_configuration_url}' => $this->context->link->getModuleLink('npsprzelewy24', 'PaymentSettings'),
            );
            Mail::Send($this->context->language->id,
                'account_active',
                $this->l('Seller account activated'),
                $mail_params,
                $customer->email,
                null,
                null,
                null,
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

    public function initContent() {
        if ($this->action == 'select_delete')
            $this->context->smarty->assign(array(
                'delete_form' => true,
                'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
                'boxes' => $this->boxes,
            ));

        parent::initContent();
    }

    public function initToolbarTitle() {
        parent::initToolbarTitle();

        switch ($this->display) {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage your Sellers');
                break;
            case 'edit':
                if (($seller = $this->loadObject(true)) && Validate::isLoadedObject($seller))
                    $this->toolbar_title[] = sprintf($this->l('Editing Seller: %s'), Tools::substr($seller->name, 0, 1));
                break;
            case 'view':
                if (($seller = $this->loadObject(true)) && Validate::isLoadedObject($seller))
                    $this->toolbar_title[] = sprintf($this->l('Seller Details: %s'), Tools::substr($seller->name, 0, 1));
                break;
        }
    }

    public function initProcess() {
        parent::initProcess();

        if (Tools::isSubmit('changeLockedVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_locked_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        } else if (Tools::isSubmit('changeOuterAddsVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_outer_adds_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        } else if (Tools::isSubmit('changeActiveProduct') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_active_product';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
    }

    public function processChangeActiveProduct() {
        $obj = $this->loadObject(true);
        $ctx = $this->context;
        $p_id = trim(Tools::getValue('id_product'));
        $product = new Product((int)$p_id);
        $default_product = new Product((int)$p_id, false, null, (int)$product->id_shop_default);
        $is_active = $default_product->active;
        if ($default_product->id == null)
            $this->errors[] = sprintf($this->l('Invalid product ID: %s'), $p_id);

        if(!$is_active) {
            if (!$obj->active)
                $this->errors[] = $this->l('Seller of this product has not activated account');
            else if($obj->locked)
                $this->errors[] = $this->l('Seller of this product has locked account');
    
            $payment_config = new P24SellerCompany(null, $obj->id);
            if ($payment_config->id == null)
                $this->errors[] = $this->l('Seller of this product has not configured payment account');
        }
        if (count($this->errors))
            return;
        $default_product->toggleStatus();
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
        'tinymce' => true,
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
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'required' => true,
                    'lang' => true,
                    'autoload_rte' => 'rte', //Enable TinyMCE editor
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Seller Name'),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('KRS'),
                    'name' => 'krs',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('The KRS registration authority'),
                    'name' => 'krs_reg',
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
                    'type' => 'textarea',
                    'label' => $this->l('Company Regulations'),
                    'name' => 'regulations',
                    'lang' => true,
                    'autoload_rte' => 'rte', //Enable TinyMCE editor
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

    public function initPageHeaderToolbar() {
        parent::initPageHeaderToolbar();

        if ($this->display != 'edit' && $this->display != 'add' && $this->display != 'view') {

            $this->page_header_toolbar_btn['new_seller'] = array(
                'href' => self::$currentIndex.'&addseller&token='.$this->token,
                'desc' => $this->l('Add new seller'),
                'icon' => 'process-icon-new'
            );
        }
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

    public function setMedia() {
        parent::setMedia();

        $this->addJqueryUI('ui.datepicker');
    }

    public function renderView() {
        $obj = $this->loadObject(true);

        $this->tpl_view_vars = array(
            'HOOK_ADMIN_SELLER_VIEW' => Hook::exec('adminSellerView'),
            'seller' => $obj,
            'products' => $this->getProducts($obj),
            'currency' => $this->context->currency,
            'lang_id' =>  $this->context->language->id,
            'payment' => new P24SellerCompany(null, $obj->id),
            'address' => new Address($obj->id_address),
            'customer' => new Customer($obj->id_customer),
            'token' => $this->token,
            'payment_token' => Tools::getAdminTokenLite('AdminSellerCompany')
         );

        $helper = new HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;
        $helper->base_folder = _PS_MODULE_DIR_.'npsmarketplace/views/templates/admin/';
        if (!is_null($this->base_tpl_view))
            $helper->base_tpl = $this->base_tpl_view;
        $view = $helper->generateView();

        return $view;
    }

    private function getProducts($seller) {
        $result = array();
        $products = $seller -> getProducts();
        foreach ($products as $product) {
            $cover = Product::getCover($product->id);
            $have_image = !empty($cover);
            $result[] = array(
                'id' => $product->id,
                'haveImage' => $have_image,
                'cover' => $have_image ? $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $cover['id_image'], 'cart_default') : null,
                'name' => Product::getProductName($product->id),
                'description' => $product->description_short[$this->context->language->id],
                'price' => $product->getPrice(),
                'quantity' => Product::getQuantity($product->id),
                'active' => $product->active,
                'active_url' => 'index.php?tab=AdminSellersAccounts&id_seller='.$seller->id.'&id_product='.(int)$product->id.'&changeActiveProduct&viewseller&token='.Tools::getAdminTokenLite('AdminSellersAccounts'),
            );
        }
        return $result;
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