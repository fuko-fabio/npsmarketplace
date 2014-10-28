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

class NpsMenuTopLinks
{
	public static function gets($id_lang, $id_npslinksmenutop = null, $id_shop)
	{
		$sql = 'SELECT l.id_npslinksmenutop, l.new_window, ll.link, ll.label
				FROM '._DB_PREFIX_.'npslinksmenutop l
				LEFT JOIN '._DB_PREFIX_.'npslinksmenutop_lang ll ON (l.id_npslinksmenutop = ll.id_npslinksmenutop AND ll.id_lang = '.(int)$id_lang.' AND ll.id_shop='.(int)$id_shop.')
				WHERE 1 '.((!is_null($id_npslinksmenutop)) ? ' AND l.id_npslinksmenutop = "'.(int)$id_npslinksmenutop.'"' : '').'
				AND l.id_shop IN (0, '.(int)$id_shop.')';

		return Db::getInstance()->executeS($sql);
	}

	public static function get($id_npslinksmenutop, $id_lang, $id_shop)
	{
		return self::gets($id_lang, $id_npslinksmenutop, $id_shop);
	}

	public static function getLinkLang($id_npslinksmenutop, $id_shop)
	{
		$ret = Db::getInstance()->executeS('
			SELECT l.id_npslinksmenutop, l.new_window, ll.link, ll.label, ll.id_lang
			FROM '._DB_PREFIX_.'npslinksmenutop l
			LEFT JOIN '._DB_PREFIX_.'npslinksmenutop_lang ll ON (l.id_npslinksmenutop = ll.id_npslinksmenutop AND ll.id_shop='.(int)$id_shop.')
			WHERE 1
			'.((!is_null($id_npslinksmenutop)) ? ' AND l.id_npslinksmenutop = "'.(int)$id_npslinksmenutop.'"' : '').'
			AND l.id_shop IN (0, '.(int)$id_shop.')
		');

		$link = array();
		$label = array();
		$new_window = false;

		foreach ($ret as $line)
		{
			$link[$line['id_lang']] = Tools::safeOutput($line['link']);
			$label[$line['id_lang']] = Tools::safeOutput($line['label']);
			$new_window = (bool)$line['new_window'];
		}

		return array('link' => $link, 'label' => $label, 'new_window' => $new_window);
	}

	public static function add($link, $label, $newWindow = 0, $id_shop)
	{
		if(!is_array($label))
			return false;
		if(!is_array($link))
			return false;

		Db::getInstance()->insert(
			'npslinksmenutop',
			array(
				'new_window'=>(int)$newWindow,
				'id_shop' => (int)$id_shop
			)
		);
		$id_npslinksmenutop = Db::getInstance()->Insert_ID();

		foreach ($label as $id_lang=>$label)
		Db::getInstance()->insert(
			'npslinksmenutop_lang',
			array(
				'id_npslinksmenutop'=>(int)$id_npslinksmenutop,
				'id_lang'=>(int)$id_lang,
				'id_shop'=>(int)$id_shop,
				'label'=>pSQL($label),
				'link'=>pSQL($link[$id_lang])
			)
		);
	}

	public static function update($link, $labels, $newWindow = 0, $id_shop, $id_link)
	{
		if(!is_array($labels))
			return false;
		if(!is_array($link))
			return false;

		Db::getInstance()->update(
			'npslinksmenutop',
			array(
				'new_window'=>(int)$newWindow,
				'id_shop' => (int)$id_shop
			),
			'id_npslinksmenutop = '.(int)$id_link
		);

		foreach ($labels as $id_lang => $label)
			Db::getInstance()->update(
				'npslinksmenutop_lang',
				array(
					'id_shop'=>(int)$id_shop,
					'label'=>pSQL($label),
					'link'=>pSQL($link[$id_lang])
				),
				'id_npslinksmenutop = '.(int)$id_link.' AND id_lang = '.(int)$id_lang
			);
	}


	public static function remove($id_npslinksmenutop, $id_shop)
	{
		Db::getInstance()->delete('npslinksmenutop', 'id_npslinksmenutop = '.(int)$id_npslinksmenutop.' AND id_shop = '.(int)$id_shop);
		Db::getInstance()->delete('npslinksmenutop_lang', 'id_npslinksmenutop = '.(int)$id_npslinksmenutop);
	}

}
