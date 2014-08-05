<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class SellerCommentCriterion
{
	/**
	 * Add a Comment Criterion
	 *
	 * @return boolean succeed
	 */
	public static function add($id_lang, $name)
	{
		if (!Validate::isUnsignedId($id_lang) ||
			!Validate::isMessage($name))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'seller_comment_criterion`
		(`id_lang`, `name`) VALUES(
		'.(int)($id_lang).',
		\''.pSQL($name).'\')'));
	}
	
	/**
	 * Link a Comment Criterion to a seller
	 *
	 * @return boolean succeed
	 */
	public static function addToSeller($id_seller_comment_criterion, $id_seller)
	{
		if (!Validate::isUnsignedId($id_seller_comment_criterion) ||
			!Validate::isUnsignedId($id_seller))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'seller_comment_criterion_seller`
		(`id_seller_comment_criterion`, `id_seller`) VALUES(
		'.(int)($id_seller_comment_criterion).',
		'.(int)($id_seller).')'));
	}
	
	/**
	 * Add grade to a criterion
	 *
	 * @return boolean succeed
	 */
	public static function addGrade($id_seller_comment, $id_seller_comment_criterion, $grade)
	{
		if (!Validate::isUnsignedId($id_seller_comment) ||
			!Validate::isUnsignedId($id_seller_comment_criterion))
			die(Tools::displayError());
		if ($grade < 0)
			$grade = 0;
		else if ($grade > 10)
			$grade = 10;
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'seller_comment_grade`
		(`id_seller_comment`, `id_seller_comment_criterion`, `grade`) VALUES(
		'.(int)($id_seller_comment).',
		'.(int)($id_seller_comment_criterion).',
		'.(int)($grade).')'));
	}
	
	/**
	 * Update criterion
	 *
	 * @return boolean succeed
	 */
	public static function update($id_seller_comment_criterion, $id_lang, $name)
	{
		if (!Validate::isUnsignedId($id_seller_comment_criterion) ||
			!Validate::isUnsignedId($id_lang) ||
			!Validate::isMessage($name))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'seller_comment_criterion` SET
		`name` = \''.pSQL($name).'\'
		WHERE `id_seller_comment_criterion` = '.(int)($id_seller_comment_criterion).' AND
		`id_lang` = '.(int)($id_lang)));
	}
	
	/**
	 * Get criterion by Seller
	 *
	 * @return array Criterion
	 */
	public static function getBySeller($id_seller, $id_lang)
	{
		if (!Validate::isUnsignedId($id_seller) ||
			!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		return (Db::getInstance()->executeS('
		SELECT pcc.`id_seller_comment_criterion`, pcc.`name`
		FROM `'._DB_PREFIX_.'seller_comment_criterion` pcc
		INNER JOIN `'._DB_PREFIX_.'seller_comment_criterion_seller` pccp ON pcc.`id_seller_comment_criterion` = pccp.`id_seller_comment_criterion`
		WHERE pccp.`id_seller` = '.(int)($id_seller).' AND 
		pcc.`id_lang` = '.(int)($id_lang)));
	}
	
	/**
	 * Get Criterions
	 *
	 * @return array Criterions
	 */
	public static function get($id_lang)
	{
		if (!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		return (Db::getInstance()->executeS('
		SELECT pcc.`id_seller_comment_criterion`, pcc.`name`
		  FROM `'._DB_PREFIX_.'seller_comment_criterion` pcc
		WHERE pcc.`id_lang` = '.(int)($id_lang).'
		ORDER BY pcc.`name` ASC'));
	}
	
	/**
	 * Delete seller criterion by seller
	 *
	 * @return boolean succeed
	 */
	public static function deleteBySeller($id_seller)
	{
		if (!Validate::isUnsignedId($id_seller))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'seller_comment_criterion_seller`
		WHERE `id_seller` = '.(int)($id_seller)));
	}
	
	/**
	 * Delete all reference of a criterion
	 *
	 * @return boolean succeed
	 */
	public static function delete($id_seller_comment_criterion)
	{
		if (!Validate::isUnsignedId($id_seller_comment_criterion))
			die(Tools::displayError());
		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'seller_comment_grade`
		WHERE `id_seller_comment_criterion` = '.(int)($id_seller_comment_criterion));
		if ($result === false)
			return ($result);
		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'seller_comment_criterion_seller`
		WHERE `id_seller_comment_criterion` = '.(int)($id_seller_comment_criterion));
		if ($result === false)
			return ($result);
		return (Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'seller_comment_criterion`
		WHERE `id_seller_comment_criterion` = '.(int)($id_seller_comment_criterion)));
	}
};