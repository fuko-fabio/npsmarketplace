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

class NpsNewProducts extends Module
{
	protected static $cache_new_products;

	public function __construct()
	{
		$this->name = 'npsnewproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.9.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('nps New products block');
		$this->description = $this->l('Displays a block featuring your store\'s newest products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		$success = (parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
			&& Configuration::updateValue('NPS_NEW_PRODUCTS_NBR', 5)
			&& $this->registerHook('displayHome')
		);
		$this->_clearCache('*');

		return $success;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitNpsNewProducts'))
		{
			if (!($productNbr = Tools::getValue('NPS_NEW_PRODUCTS_NBR')) || empty($productNbr))
				$output .= $this->displayError($this->l('Please complete the "products to display" field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else
			{
				Configuration::updateValue('PS_NB_DAYS_NPS_NEW_PRODUCT', (int)(Tools::getValue('PS_NB_DAYS_NPS_NEW_PRODUCT')));
                Configuration::updateValue('PS_NPS_NEWPRODUCTS_DISPLAY', (int)(Tools::getValue('PS_NPS_NEWPRODUCTS_DISPLAY')));
				Configuration::updateValue('NPS_NEW_PRODUCTS_NBR', (int)($productNbr));
				$this->_clearCache('*');
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}

	private function getNewProducts()
	{
		if (!Configuration::get('NPS_NEW_PRODUCTS_NBR'))
			return;
		$newProducts = false;
		if (Configuration::get('PS_NB_DAYS_NPS_NEW_PRODUCT'))
			$newProducts = Product::getNewProducts((int) $this->context->language->id, 0, (int)Configuration::get('NPS_NEW_PRODUCTS_NBR'));

		if (!$newProducts && Configuration::get('PS_NPS_NEWPRODUCTS_DISPLAY'))
			return;
		return NpsMarketplace::filterByTown($newProducts);
	}

	protected function getCacheId($name = null)
	{
		if ($name === null)
			$name = 'npsnewproducts';
		return parent::getCacheId($name.'|'.date('Ymd'));
	}

	public function hookDisplayHome($params)
	{
	    if (!isset(NpsNewProducts::$cache_new_products))
            NpsNewProducts::$cache_new_products = $this->getNewProducts();

		if (!$this->isCached('npsnewproducts_home.tpl', $this->getCacheId($this->name.$this->context->cookie->main_town)))
		{
			$this->smarty->assign(array(
				'new_products' => NpsNewProducts::$cache_new_products,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (NpsNewProducts::$cache_new_products === false)
			return false;

		return $this->display(__FILE__, 'npsnewproducts_home.tpl', $this->getCacheId($this->name.$this->context->cookie->main_town));
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');

		$this->context->controller->addCSS($this->_path.'npsnewproducts.css', 'all');
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

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('npsnewproducts.tpl');
		parent::_clearCache('npsnewproducts_home.tpl', 'npsnewproducts-home');
		parent::_clearCache('tab.tpl', 'npsnewproducts-tab');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Products to display'),
						'name' => 'NPS_NEW_PRODUCTS_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in this block.')
					),
					array(
						'type'  => 'text',
						'label' => $this->l('Number of days for which the product is considered \'new\''),
						'name'  => 'PS_NB_DAYS_NPS_NEW_PRODUCT',
						'class' => 'fixed-width-xs',
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_NPS_NEWPRODUCTS_DISPLAY',
						'desc' => $this->l('Show the block even if no new products are available.'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitNpsNewProducts';
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
			'PS_NB_DAYS_NPS_NEW_PRODUCT' => Tools::getValue('PS_NB_DAYS_NPS_NEW_PRODUCT', Configuration::get('PS_NB_DAYS_NPS_NEW_PRODUCT')),
			'PS_NPS_NEWPRODUCTS_DISPLAY' => Tools::getValue('PS_NPS_NEWPRODUCTS_DISPLAY', Configuration::get('PS_NPS_NEWPRODUCTS_DISPLAY')),
			'NPS_NEW_PRODUCTS_NBR' => Tools::getValue('NPS_NEW_PRODUCTS_NBR', Configuration::get('NPS_NEW_PRODUCTS_NBR')),
		);
	}
}
