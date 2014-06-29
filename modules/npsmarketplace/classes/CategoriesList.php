<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

class CategoriesList
{
    public $context;
    
    public function __construct($context = null)
    {
        $this->context = $context;
    }

    public function getTree()
    {
        $resultIds = array();
        $resultParents = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
        FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
        WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
        AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
        AND c.id_category IN (
            SELECT id_category
            FROM `'._DB_PREFIX_.'category_group`
            WHERE `id_group` IN ('.pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).')
        )
        ORDER BY `level_depth` ASC, cs.`position` ASC');
        foreach ($result as &$row)
        {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }
        $blockCategTree = $this->getCategoriesTree($resultParents, $resultIds);

        return $blockCategTree;
    }

    private function getCategoriesTree($resultParents, $resultIds, $maxDepth = 0, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category))
            $id_category = $this->context->shop->getCategory();

        $children = array();
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
            foreach ($resultParents[$id_category] as $subcat)
                $children[] = $this->getCategoriesTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);

        if (!isset($resultIds[$id_category]))
            return false;
        
        $return = array(
            'id' => $id_category,
            'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
            'name' =>  $resultIds[$id_category]['name'],
            'desc'=>  $resultIds[$id_category]['description'],
            'children' => $children
        );

        return $return;
    }
}
?>