<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class CategoriesList {
    public $context;

    public function __construct($context = null) {
        $this->context = $context;
    }

    public function getList($wanted_ids = null) {
        $categories = $this->getCategories();
        $res = array();
        if ($wanted_ids) {
            foreach ($categories as $key => $item) {
                if (in_array($item['id_category'], $wanted_ids)) {
                    $res[$key] = $item;
                }
            }
        } else {
            $res = $categories;
        }
        $result = array();
        foreach ($res as $key => $item) {
            $result[] = array(
                'id' => $item['id_category'],
                'link' => $this->context->link->getCategoryLink($item['id_category'], $item['link_rewrite']),
                'name' =>  $item['name'],
                'desc'=>  $item['description'],
                'id_parent'=>  $item['id_parent'],
                'children' => array()
            );
        }
        return $result;
    }

    public function getTree($excluded_ids = null) {
        $resultIds = array();
        $resultParents = array();
        $result = $this->getCategories();

        $res = array();
        if ($excluded_ids) {
            foreach ($result as $key => $item) {
                if (!in_array($item['id_category'], $excluded_ids)) {
                    $res[$key] = $item;
                }
            }
        } else {
            $res = $result;
        }

        foreach ($res as &$row) {
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
            'id_parent'=>  $resultIds[$id_category]['id_parent'],
            'children' => $children
        );

        return $return;
    }
    
    private function getCategories() {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
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
    }
}
?>