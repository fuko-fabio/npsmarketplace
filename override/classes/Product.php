<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductAttributeExpiryDate.php');

class Product extends ProductCore
{
    public function delete()
    {
        if(parent::delete())
            return $this->deleteSellersAssociations();
        else
            return false;
    }

    public function deleteSellersAssociations() {
        return Db::getInstance()->delete('seller_product', 'id_product = '.(int)$this->id);
    }

    public function newEventCombination($date, $time, $quantity, $expiry_date, $id_shop = null) {
        $d = array();
        $t = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $d[$lang['id_lang']] = $date;
            $t[$lang['id_lang']] = $time;
        }
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $date_attr_group_id = Configuration::get('NPS_ATTRIBUTE_DATE_ID');
        $time_attr_group_id = Configuration::get('NPS_ATTRIBUTE_TIME_ID');

        $res = $this->getAttributeId($date_attr_group_id, $date, $lang_id);
        if(!$res)
            $date_attr_id = null;
        else
            $date_attr_id = $res['id_attribute'];
        $date_attr = new Attribute($date_attr_id);
        if ($date_attr_id == null) {
            $date_attr->name = $d;
            $date_attr->id_attribute_group = $date_attr_group_id;
            $date_attr->position = -1;
            $date_attr->save();
        }

        $res = $this->getAttributeId($time_attr_group_id, $time, $lang_id);
        if(!$res)
            $time_attr_id = null;
        else
            $time_attr_id = $res['id_attribute'];
        $time_attr = new Attribute($time_attr_id);
        if ($time_attr_id == null) {
            $time_attr->name = $t;
            $time_attr->id_attribute_group = $time_attr_group_id;
            $time_attr->position = -1;
            $time_attr->save();
        }
        
        $default_attribute = Product::getDefaultAttribute($this->id);
        $default = true;
        if ($default_attribute)
            $default = false;

        $id_product_attribute = $this->addAttribute(
            0,//$price,
            null,//$weight,
            null,//$unit_impact,
            null,//$ecotax,
            null,//$id_images,
            null,//$reference,
            null,//$ean13,
            $default,//$default
            null,//$location
            null,//$upc 
            1,//$minimal_quantity
            array(),//$id_shop_list
            null);//$available_date

        $combination = new Combination((int)$id_product_attribute);
        $combination->setAttributes(array($date_attr->id, $time_attr->id));
        StockAvailable::setQuantity((int)$this->id, (int)$id_product_attribute, $quantity, $id_shop);
        $this->saveExpiryDate($id_product_attribute, $expiry_date);
        Search::indexation(false, $this->id);
    }

    private function saveExpiryDate($id_product_attribute, $expiry_date) {
        $e_d = new ProductAttributeExpiryDate();
        $e_d->expiry_date = $expiry_date;
        $e_d->id_product_attribute = $id_product_attribute;
        $e_d->save();
    }

    private function getAttributeId($id_attribute_group, $name, $id_lang) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'attribute_group` ag
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute` a
                ON a.`id_attribute_group` = ag.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
            '.Shop::addSqlAssociation('attribute_group', 'ag').'
            '.Shop::addSqlAssociation('attribute', 'a').'
            WHERE al.`name` = \''.pSQL($name).'\' AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
            ORDER BY agl.`name` ASC, a.`position` ASC
        ');

        return $result ? $result : null;
    }


    /**
    * Get products by ids
    *
    * @param integer $id_lang Language id
    * @param array   $ids products ids
    * @param integer $pageNumber Start from (optional)
    * @param integer $nbProducts Number of products to return (optional)
    * @return array New products
    */
    public static function getProductsByIds($id_lang, $ids = array(), $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null) {
        if (!$context)
            $context = Context::getContext();

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
            $front = false;

        if ($page_number < 0) $page_number = 0;
        if ($nb_products < 1) $nb_products = 10;
        if (empty($order_by) || $order_by == 'position') $order_by = 'date_add';
        if (empty($order_way)) $order_way = 'DESC';
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add'  || $order_by == 'date_upd')
            $order_by_prefix = 'p';
        else if ($order_by == 'name')
            $order_by_prefix = 'pl';
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
            die(Tools::displayError());

        $sql_groups = '';
        if (Group::isFeatureActive())
        {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = 'AND p.`id_product` IN (
                SELECT cp.`id_product`
                FROM `'._DB_PREFIX_.'category_group` cg
                LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
                WHERE cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
            )';
        }

        if (strpos($order_by, '.') > 0)
        {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        if ($count)
        {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
                    FROM `'._DB_PREFIX_.'product` p
                    '.Shop::addSqlAssociation('product', 'p').'
                    WHERE product_shop.`active` = 1
                    AND p.`id_product` IN ('.implode(',', $ids).')
                    '.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
                    '.$sql_groups;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
            product_shop.`date_add`'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', '
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image', 'i', 'i.`id_product` = p.`id_product`');
        $sql->join(Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1'));
        $sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('product_shop.`active` = 1');
        if ($front)
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        $sql->where('p.`id_product` IN ('.implode(',', $ids).')');
        if (Group::isFeatureActive())
            $sql->where('p.`id_product` IN (
                SELECT cp.`id_product`
                FROM `'._DB_PREFIX_.'category_group` cg
                LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
                WHERE cg.`id_group` '.$sql_groups.'
            )');
        $sql->groupBy('product_shop.id_product');

        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way));
        $sql->limit($nb_products, $page_number * $nb_products);

        if (Combination::isFeatureActive())
        {
            $sql->select('MAX(product_attribute_shop.id_product_attribute) id_product_attribute');
            $sql->leftOuterJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
            $sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on = 1'));
        }
        $sql->join(Product::sqlStock('p', Combination::isFeatureActive() ? 'product_attribute_shop' : 0));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($order_by == 'price')
            Tools::orderbyPrice($result, $order_way);
        if (!$result)
            return false;

        $products_ids = array();
        foreach ($result as $row)
            $products_ids[] = $row['id_product'];
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);
        return Product::getProductsProperties((int)$id_lang, $result);
    }

    /**
    * Get all available product attributes combinations
    *
    * @param integer $id_lang Language id
    * @return array Product attributes combinations
    */
    public static function getStaticAttributeCombinations($id_product, $id_lang)
    {
        if (!Combination::isFeatureActive())
            return array();

        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                    a.`id_attribute`, pa.`unit_price_impact`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                '.Shop::addSqlAssociation('product_attribute', 'pa').'
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                WHERE pa.`id_product` = '.(int)$id_product.'
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute`';

        $res = Db::getInstance()->executeS($sql);

        //Get quantity of each variations
        foreach ($res as $key => $row)
        {
            $cache_key = $row['id_product'].'_'.$row['id_product_attribute'].'_quantity';

            if (!Cache::isStored($cache_key))
                Cache::store(
                    $cache_key,
                    StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute'])
                );

            $res[$key]['quantity'] = Cache::retrieve($cache_key);
        }

        return $res;
    }
}
?>