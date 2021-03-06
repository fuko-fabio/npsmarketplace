<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductAttributeExpiryDate.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.php');

class Product extends ProductCore {

    public function delete() {
        if(parent::delete())
            return $this->deleteSellersAssociations() && $this->deleteExtras();
        else
            return false;
    }

    public function deleteSellersAssociations() {
        return Db::getInstance()->delete('seller_product', 'id_product = '.(int)$this->id);
    }

    public function deleteExtras() {
        return ProductAttributeExpiryDate::deleteByProductId($this->id);
    }

    public function createCombination($combination, DateTime $date_time, $id_shop = null) {
        $attributes_ids = array();
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        if ($combination['type'] != 'ticket' && $combination['type'] != 'carnet') {
            $combination['price'] = 0;
            $combination['quantity'] = 1;
            $combination['name'] = 'advertisment';
        }

        if ($combination['type'] != 'carnet') {
            if (isset($combination['date']) && !empty($combination['date'])) {
                $dt = new DateTime($combination['date']);
                $attributes_ids[] = $this->saveAttribute(Configuration::get('NPS_ATTRIBUTE_DATE_ID'), $dt->format('Y-m-d'), $id_lang);
                $attributes_ids[] = $this->saveAttribute(Configuration::get('NPS_ATTRIBUTE_TIME_ID'), $dt->format('H:i'), $id_lang);
            }
        }
        if (isset($combination['name']) && !empty($combination['name']))
            $attributes_ids[] = $this->saveAttribute(Configuration::get('NPS_ATTRIBUTE_NAME_ID'), $combination['name'], $id_lang);

        if (isset($combination['type']) && !empty($combination['type']))
            $attributes_ids[] = $this->saveAttribute(Configuration::get('NPS_ATTRIBUTE_TYPE_ID'), $combination['type'], $id_lang);

        $id_product_attribute = $this->addAttribute(
            $combination['price'],//$price,
            null,//$weight,
            null,//$unit_impact,
            null,//$ecotax,
            null,//$id_images,
            null,//$reference,
            null,//$ean13,
            isset($combination['default']) ? $combination['default'] : false,//$default
            null,//$location
            null,//$upc 
            1,//$minimal_quantity
            array(),//$id_shop_list
            null);//$available_date

        $comb = new Combination((int)$id_product_attribute);
        $comb->setAttributes($attributes_ids);
        StockAvailable::setQuantity((int)$this->id, (int)$id_product_attribute, $combination['quantity'], $id_shop);
        $this->saveExpiryDate($date_time, $id_product_attribute);
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_add = NOW() WHERE id_product = '.$this->id);
        Search::indexation(false, $this->id);
        return $comb;
    }

    private function saveAttribute($id_attribute_group, $value, $id_lang) {
        $res = $this->getAttributeId($id_attribute_group, $value, $id_lang);
        if (!$res)
            $id_attribute = null;
        else
            $id_attribute = $res['id_attribute'];

        $attr = new Attribute($id_attribute);
        if ($id_attribute == null) {
            $v = array();
            foreach (Language::getLanguages() as $key => $lang) {
                $v[$lang['id_lang']] = $value;
            }
            $attr->name = $v;
            $attr->id_attribute_group = $id_attribute_group;
            $attr->position = -1;
            $attr->save();
        }
        return $attr->id;
    }

    public function saveExpiryDate(DateTime $date_time, $id_product_attribute = null) {
        $e_d = new ProductAttributeExpiryDate();
        $e_d->expiry_date = $date_time->format('Y-m-d H:i:s');
        $e_d->id_product_attribute = $id_product_attribute;
        $e_d->id_product = $this->id;
        $e_d->save();
    }

    public function getAttributesIds() {
        $result = array();
        if ($this->id) {
            $r = Db::getInstance()->executeS(
                'SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.$this->id
            );
            foreach ($r as $key => $value) {
                $result[] = $value['id_product_attribute'];
            }
        }
        return $result;
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
        if(empty($ids))
            return array();

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
    public static function getStaticAttributeCombinations($id_product, $id_lang = null)
    {
        if (!Combination::isFeatureActive())
            return array();

        if ($id_lang == null)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

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

    public static function showSellerDetails($id_product) {
        foreach(Product::getStaticAttributeCombinations($id_product) as $key => $value) {
            if ($value['id_attribute_group'] == Configuration::get('NPS_ATTRIBUTE_TYPE_ID') && $value['attribute_name'] == 'externalad')
                return false;
        }
        return true;
    }

    public static function isAdvertisment($id_product) {
        foreach(Product::getStaticAttributeCombinations($id_product) as $key => $value) {
            if ($value['id_attribute_group'] == Configuration::get('NPS_ATTRIBUTE_TYPE_ID') && ($value['attribute_name'] == 'ad' || $value['attribute_name'] == 'externalad'))
                return true;
        }
        return false;
    }

    public static function getExtras($id_product, $id_lang = null) {
        if (!isset($id_product))
            return null;
        
        if ($id_lang == null)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $sql = 'SELECT `video`, `lat`, `lng`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_product` = '.$id_product;
        $result = Db::getInstance()->getRow($sql);
        $result['advertisment'] = Product::isAdvertisment($id_product);
        $result['external_advertisment'] = !Product::showSellerDetails($id_product);
        return $result;
    }

    public function persistExtraInfo($lat, $lng, $video_url) {
        return Db::getInstance()->update('product', array(
            'lng' => $lng,
            'lat' => $lat,
            'video' => $video_url
        ), 'id_product = '.$this->id);
    }

    public static function removeSpecialPrice($id_product) {
        SpecificPrice::deleteByProductId($id_product);
        $p = new Product($id_product);
        $p->on_sale = 0;
        $p->save();
        return array(
            'result' => 1,
            'errors' => array()
        );
    }
    
    public static function addSpecialPrice($id_product, $id_product_attribute, $reduction, $from = '0000-00-00 00:00:00', $to = '0000-00-00 00:00:00') {
        $id_shop = 0;
        $id_currency = 0;
        $id_country = 0;
        $id_group = 0;
        $id_customer = 0;
        $id_combination = 0;
        $price = -1;
        $from_quantity = 1;
        $reduction_type = 'amount';

        $nps = new NpsMarketplace();
        $errors = array();
        $prices = SpecificPrice::getIdsByProductId($id_product);
        if ($prices)
            $errors[] = $nps->l('A specific price already exists for tis event.');
        elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction)))
            $errors[] = $nps->l('Invalid price/discount amount');
        elseif ($reduction && !Validate::isReductionType($reduction_type))
            $errors[] = $nps->l('Please select a discount type (amount or percentage).');
        elseif (SpecificPrice::exists((int)$id_product, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false))
            $errors[] = $nps->l('A specific price already exists for tis event.');
        else

        if (empty($errors)) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int)$id_product;
            $specificPrice->id_product_attribute = (int)$id_product_attribute;
            $specificPrice->id_shop = (int)$id_shop;
            $specificPrice->id_currency = (int)($id_currency);
            $specificPrice->id_country = (int)($id_country);
            $specificPrice->id_group = (int)($id_group);
            $specificPrice->id_customer = (int)$id_customer;
            $specificPrice->price = (float)($price);
            $specificPrice->from_quantity = (int)($from_quantity);
            $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
            $specificPrice->reduction_type = $reduction_type;
            $specificPrice->from = $from;
            $specificPrice->to = $to;
            if (!$specificPrice->add())
                $errors[] = $nps->l('An error occurred while updating the specific price.');
        }
        $p = new Product($id_product);
        $p->on_sale = 1;
        $p->save();
        return array(
            'result' => empty($errors) ? 1 : 0,
            'errors' => $errors
        );
    }

    /**
    * Get new products
    *
    * @param integer $id_lang Language id
    * @param integer $pageNumber Start from (optional)
    * @param integer $nbProducts Number of products to return (optional)
    * @return array New products
    */
    public static function getNewProductsByLocation($id_lang, $page_number = 0, $nb_products = 10, $count = false,
            $nb_days = 20, $order_by = null, $order_way = null, Context $context = null, $types = null)
    {
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

        $id_feature_value = null;
        $id_town = $context->cookie->main_town;
        $id_province = $context->cookie->main_province;
        if ($id_town > 0) {
            $t = new Town($id_town);
            $dbquery = new DbQuery();
            $dbquery->select('id_feature_value')
                ->from('feature_value_lang')
                ->where('`value` = \''.$t->name[$context->language->id].'\'');
            $id_feature_value = Db::getInstance()->getValue($dbquery);
            if (!$id_feature_value)
                if ($get_total)
                    return 0;
                else 
                    return array();
        } else if($id_province > 0) {
            $province = new Province($id_province);
            $dbquery = new DbQuery();
            $dbquery->select('id_feature_value')
                ->from('feature_value_lang')
                ->where('`value` = \''.$province->name[$context->language->id].'\'');
            $id_feature_value = Db::getInstance()->getValue($dbquery);
            if (!$id_feature_value)
                if ($get_total)
                    return 0;
                else 
                    return array();
        }

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

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
                    FROM `'._DB_PREFIX_.'product` p
                    '.Shop::addSqlAssociation('product', 'p').'
                    WHERE product_shop.`active` = 1
                    AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days.' DAY')).'"
                    '.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
                    '.($id_feature_value ? ' AND p.id_product IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp
                    WHERE fp.`id_feature_value` = '.(int)$id_feature_value.')' : '').$sql_groups;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
            product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days.' DAY')).'" as new'
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

        if ($id_feature_value)
            $sql->where('p.`id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp
                    WHERE fp.`id_feature_value` = '.(int)$id_feature_value.')');

        $sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days.' DAY')).'"');
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

            $sql->leftJoin('product_attribute_combination', 'pac', 'pac.`id_product_attribute` = pa.`id_product_attribute`');
            $sql->leftJoin('attribute', 'a', 'a.`id_attribute` = pac.`id_attribute`');
            $sql->leftJoin('attribute_group', 'ag', 'ag.`id_attribute_group` = a.`id_attribute_group`');
            $sql->leftJoin('attribute_lang', 'al', '(a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')');
            $sql->leftJoin('attribute_group_lang', 'agl', '(ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')');
            
            if ($types != null && !empty($types)) {
                $q = '';
                foreach ($types as $type) {
                    $q = $q.' al.`name` = "'.$type.'" OR';
                }
                $sql->where(rtrim($q, 'OR'));
            }
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

}
?>