<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
require_once(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.php');

class NpsFeatured extends Module
{
	protected static $cache_products;

	public function __construct()
	{
		$this->name = 'npsfeatured';
		$this->tab = 'front_office_features';
		$this->version = '1.6';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('nps Featured products on the homepage');
		$this->description = $this->l('Displays featured products in the central column of your homepage.');
	}

	public function install()
	{
		$this->_clearCache('*');
		Configuration::updateValue('NPS_HOME_FEATURED_NBR', 8);
		Configuration::updateValue('NPS_HOME_FEATURED_CAT', (int)Context::getContext()->shop->getCategory());
        Configuration::updateValue('NPS_HOME_FEATURED_RANDOMIZE', false);
        Configuration::updateValue('NPS_HOME_FEATURED_HOOK_IFRAME', true);

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !$this->registerHook('categoryUpdate')
			|| !$this->registerHook('displayHome')
            || !$this->registerHook('iframeHome')
            || !$this->registerHook('iframeHomeHeader')
		)
			return false;

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		$errors = array();
		if (Tools::isSubmit('submitNpsFeatured'))
		{
			$nbr = Tools::getValue('NPS_HOME_FEATURED_NBR');
			if (!Validate::isInt($nbr) || $nbr <= 0)
			$errors[] = $this->l('The number of products is invalid. Please enter a positive number.');

			$cat = Tools::getValue('NPS_HOME_FEATURED_CAT');
			if (!Validate::isInt($cat) || $cat <= 0)
				$errors[] = $this->l('The category ID is invalid. Please choose an existing category ID.');
				
			$rand = Tools::getValue('NPS_HOME_FEATURED_RANDOMIZE');
			if (!Validate::isBool($rand))
				$errors[] = $this->l('Invalid value for the "randomize" flag.');
			if (isset($errors) && count($errors))
				$output = $this->displayError(implode('<br />', $errors));
			else
			{
				Configuration::updateValue('NPS_HOME_FEATURED_NBR', (int)$nbr);
				Configuration::updateValue('NPS_HOME_FEATURED_CAT', (int)$cat);
                Configuration::updateValue('NPS_HOME_FEATURED_RANDOMIZE', (bool)$rand);
                Configuration::updateValue('NPS_HOME_FEATURED_HOOK_IFRAME', (bool)Tools::getValue('NPS_HOME_FEATURED_HOOK_IFRAME'));
				Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('npsfeatured.tpl'));
				$output = $this->displayConfirmation($this->l('Your settings have been updated.'));
			}
		}

		return $output.$this->renderForm();
	}

	public function hookDisplayHeader($params)
	{
		$this->hookHeader($params);
	}

    public function hookHeader() {
        $this->context->controller->addCss(($this->_path).'npsfeatured.css');
    }

    public function hookIframeHome($params) {
        if (Configuration::get('NPS_HOME_FEATURED_HOOK_IFRAME')) {
            return $this->hookDisplayHome($params);
        }
    }

    public function hookIframeHomeHeader($params) {
        if (Configuration::get('NPS_HOME_FEATURED_HOOK_IFRAME')) {
            return '<link rel="stylesheet" href="'.$this->_path.'npsfeatured.css">';
        }
    }

	public function _cacheProducts()
	{
		if (!isset(NpsFeatured::$cache_products))
		{
			$category = new Category((int)Configuration::get('NPS_HOME_FEATURED_CAT'), (int)Context::getContext()->language->id);
			$nb = (int)Configuration::get('NPS_HOME_FEATURED_NBR');
			if (Configuration::get('NPS_HOME_FEATURED_RANDOMIZE'))
				NpsFeatured::$cache_products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
			else
				NpsFeatured::$cache_products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), 'position');
		}

		if (NpsFeatured::$cache_products === false || empty(NpsFeatured::$cache_products))
			return false;
	}
    


	public function hookDisplayHome($params)
	{

		if (!$this->isCached('npsfeatured.tpl', $this->getCacheId($this->name.$this->context->cookie->main_town)))
		{
			$this->_cacheProducts();
            
			$this->smarty->assign(
				array(
					'products' => NpsFeatured::$cache_products,
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
					'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				)
			);
		}

		return $this->display(__FILE__, 'npsfeatured.tpl', $this->getCacheId($this->name.$this->context->cookie->main_town));
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookCategoryUpdate($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('npsfeatured.tpl');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('To add products to your homepage, simply add them to the corresponding product category (default: "Home").'),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Number of products to be displayed'),
						'name' => 'NPS_HOME_FEATURED_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Category from which to pick products to be displayed'),
						'name' => 'NPS_HOME_FEATURED_CAT',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Choose the category ID of the products that you would like to display on homepage (default: 2 for "Home").'),
					),
					array( 
						'type' => 'switch',
						'label' => $this->l('Randomly display featured products'),
						'name' => 'NPS_HOME_FEATURED_RANDOMIZE',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Enable if you wish the products to be displayed randomly (default: no).'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array( 
                        'type' => 'switch',
                        'label' => $this->l('Hook on shop iframe'),
                        'name' => 'NPS_HOME_FEATURED_HOOK_IFRAME',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitNpsFeatured';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'NPS_HOME_FEATURED_NBR' => Tools::getValue('NPS_HOME_FEATURED_NBR', (int)Configuration::get('NPS_HOME_FEATURED_NBR')),
			'NPS_HOME_FEATURED_CAT' => Tools::getValue('NPS_HOME_FEATURED_CAT', (int)Configuration::get('NPS_HOME_FEATURED_CAT')),
            'NPS_HOME_FEATURED_RANDOMIZE' => Tools::getValue('NPS_HOME_FEATURED_RANDOMIZE', (bool)Configuration::get('NPS_HOME_FEATURED_RANDOMIZE')),
            'NPS_HOME_FEATURED_HOOK_IFRAME' => Tools::getValue('NPS_HOME_FEATURED_HOOK_IFRAME', (bool)Configuration::get('NPS_HOME_FEATURED_HOOK_IFRAME')),
		);
	}
}
