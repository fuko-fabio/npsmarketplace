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

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class NpsFavorite extends Module
{
	public function __construct()
	{
		$this->name = 'npsfavorite';
		$this->tab = 'front_office_features';
		$this->version = '1.2.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('nps Favorite Products');
		$this->description = $this->l('Display a page featuring the customer\'s favorite products.');
		$this->ps_versions_compliancy = array('min' => '1.5.6.1', 'max' => _PS_VERSION_);
	}

	public function install()
	{
			if (!parent::install()
				|| !$this->registerHook('displayMyAccountBlock')
				|| !$this->registerHook('displayCustomerAccount')
				|| !$this->registerHook('displayLeftColumnProduct')
				|| !$this->registerHook('extraLeft')
				|| !$this->registerHook('displayHeader')
                || !$this->registerHook('displayProductListFunctionalButtons'))
					return false;

			if (!Db::getInstance()->execute('
				CREATE TABLE `'._DB_PREFIX_.'favorite_product` (
				`id_favorite_product` int(10) unsigned NOT NULL auto_increment,
				`id_product` int(10) unsigned NOT NULL,
				`id_customer` int(10) unsigned NOT NULL,
				`id_shop` int(10) unsigned NOT NULL,
				`date_add` datetime NOT NULL,
  				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_favorite_product`))
				ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
				return false;

			return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'favorite_product`'))
			return false;
		return true;
	}

    public function hookDisplayProductListFunctionalButtons($params)
    {
        if (!$this->isCached('npsfavorite_button.tpl', $this->getCacheId('npsfavorite|'.(int)$params['product']['id_product'])))
        {
            $this->smarty->assign(array(
                'product' => $params['product'],
                'isLogged' => (int)$this->context->customer->logged,
            ));
        }

        return $this->display(__FILE__, 'npsfavorite_button.tpl', $this->getCacheId('npsfavorite|'.(int)$params['product']['id_product']));
    }

	public function hookDisplayCustomerAccount($params)
	{
		$this->smarty->assign('in_footer', false);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		$this->smarty->assign('in_footer', true);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayLeftColumnProduct($params)
	{
		include_once(dirname(__FILE__).'/NpsFavoriteProduct.php');

		$this->smarty->assign(array(
			'isCustomerFavoriteProduct' => (NpsFavoriteProduct::isCustomerNpsFavoriteProduct($this->context->customer->id, Tools::getValue('id_product')) ? 1 : 0),
			'isLogged' => (int)$this->context->customer->logged,
		));
		return $this->display(__FILE__, 'npsfavorite-extra.tpl');
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'npsfavorite.css');
		$this->context->controller->addJS($this->_path.'npsfavorite.js');
		return $this->display(__FILE__, 'npsfavorite-header.tpl');
	}

}


