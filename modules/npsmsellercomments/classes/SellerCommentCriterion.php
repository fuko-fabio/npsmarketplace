<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class SellerCommentCriterion extends ObjectModel
{
	public	$id;
	public	$id_seller_comment_criterion_type;
	public	$name;
	public	$active = true;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'seller_comment_criterion',
		'primary' => 'id_seller_comment_criterion',
		'multilang' => true,
		'fields' => array(
			'id_seller_comment_criterion_type' =>	array('type' => self::TYPE_INT),
			'active' =>								array('type' => self::TYPE_BOOL),
			// Lang fields
			'name' =>								array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
		)
	);

	public function delete()
	{
		if (!parent::delete())
			return false;
		if ($this->id_seller_comment_criterion_type == 3)
		{
			if (!Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'seller_comment_criterion_seller
					WHERE id_seller_comment_criterion='.(int)$this->id))
				return false;
		}

		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'seller_comment_grade`
			WHERE `id_seller_comment_criterion` = '.(int)$this->id);
	}

	public function update($nullValues = false)
	{
		$previousUpdate = new self((int)$this->id);
		if (!parent::update($nullValues))
			return false;
		if ($previousUpdate->id_seller_comment_criterion_type != $this->id_seller_comment_criterion_type)
		{
			if ($previousUpdate->id_seller_comment_criterion_type == 3)
				return Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'seller_comment_criterion_seller
					WHERE id_seller_comment_criterion = '.(int)$previousUpdate->id);
		}
		return true;
	}

	/**
	 * Link a Comment Criterion to a seller
	 *
	 * @return boolean succeed
	 */
	public function addSeller($id_seller)
	{
		if (!Validate::isUnsignedId($id_seller))
			die(Tools::displayError());
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'seller_comment_criterion_seller` (`id_seller_comment_criterion`, `id_seller`)
			VALUES('.(int)$this->id.','.(int)$id_seller.')
		');
	}

	/**
	 * Add grade to a criterion
	 *
	 * @return boolean succeed
	 */
	public function addGrade($id_seller_comment, $grade)
	{
		if (!Validate::isUnsignedId($id_seller_comment))
			die(Tools::displayError());
		if ($grade < 0)
			$grade = 0;
		elseif ($grade > 10)
			$grade = 10;
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'seller_comment_grade`
		(`id_seller_comment`, `id_seller_comment_criterion`, `grade`) VALUES(
		'.(int)($id_seller_comment).',
		'.(int)$this->id.',
		'.(int)($grade).')'));
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

		$cache_id = 'SellerCommentCriterion::getBySeller_'.(int)$id_seller.'-'.(int)$id_lang;
		if (!Cache::isStored($cache_id))
		{
			$result = Db::getInstance()->executeS('
				SELECT pcc.`id_seller_comment_criterion`, pccl.`name`
				FROM `'._DB_PREFIX_.'seller_comment_criterion` pcc
				LEFT JOIN `'._DB_PREFIX_.'seller_comment_criterion_lang` pccl
					ON (pcc.id_seller_comment_criterion = pccl.id_seller_comment_criterion)
				LEFT JOIN `'._DB_PREFIX_.'seller_comment_criterion_seller` pccp
					ON (pcc.`id_seller_comment_criterion` = pccp.`id_seller_comment_criterion` AND pccp.`id_seller` = '.(int)$id_seller.')
				WHERE pccl.`id_lang` = '.(int)($id_lang).'
				AND (
					pccp.id_seller IS NOT NULL
					OR pcc.id_seller_comment_criterion_type = 1
				)
				AND pcc.active = 1
				GROUP BY pcc.id_seller_comment_criterion
			');
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Get Criterions
	 *
	 * @return array Criterions
	 */
	public static function getCriterions($id_lang, $type = false, $active = false)
	{
		if (!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		
		$sql = '
			SELECT pcc.`id_seller_comment_criterion`, pcc.id_seller_comment_criterion_type, pccl.`name`, pcc.active
			FROM `'._DB_PREFIX_.'seller_comment_criterion` pcc
			JOIN `'._DB_PREFIX_.'seller_comment_criterion_lang` pccl ON (pcc.id_seller_comment_criterion = pccl.id_seller_comment_criterion)
			WHERE pccl.`id_lang` = '.(int)$id_lang.($active ? ' AND active = 1' : '').($type ? ' AND id_seller_comment_criterion_type = '.(int)$type : '').'
			ORDER BY pccl.`name` ASC';
		$criterions = Db::getInstance()->executeS($sql);

		$types = self::getTypes();
		foreach ($criterions as $key => $data)
			$criterions[$key]['type_name'] = $types[$data['id_seller_comment_criterion_type']];

		return $criterions;
	}

	public function getSellers()
	{
		$res = Db::getInstance()->executeS('
			SELECT pccp.id_seller, pccp.id_seller_comment_criterion
			FROM `'._DB_PREFIX_.'seller_comment_criterion_seller` pccp
			WHERE pccp.id_seller_comment_criterion = '.(int)$this->id);
		$sellers = array();
		if ($res)
			foreach ($res AS $row)
				$sellers[] = (int)$row['id_seller'];
		return $sellers;
	}

	public function deleteSellers()
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'seller_comment_criterion_seller`
			WHERE `id_seller_comment_criterion` = '.(int)$this->id);
	}

	public static function getTypes()
	{
		// Instance of module class for translations
		$module = new SellerComments();

		return array(
			1 => $module->l('Valid for the entire catalog', 'SellerCommentCriterion'),
			2 => $module->l('Restricted to some categories', 'SellerCommentCriterion'),
			3 => $module->l('Restricted to some sellers', 'SellerCommentCriterion')
		);
	}
}
