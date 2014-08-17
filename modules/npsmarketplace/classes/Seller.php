<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_NPS_SEL_IMG_DIR_' ) )
    define('_NPS_SEL_IMG_DIR_', _PS_IMG_DIR_.'seller/');

class Seller extends ObjectModel
{
    /** @var integer id */
    public $id;

    /** @var integer Customer id */
    public $id_customer;

    /** @var string Request date */
    public $request_date;

    /** @var string Name */
    public $name;

    /** @var string e-mail */
    public $email;

    /** @var string phone */
    public $phone;

    /** @var string NIP */
    public $nip = null;

    /** @var string REGON */
    public $regon = null;

    /** @var boolean account state */
    public $active = false;

    /** @var boolean account state */
    public $requested = false;

    /** @var boolean account lock state */
    public $locked = false;

    /** @var integer NIP */
    public $commision;

    /** @var string Company name */
    public $company_name;

    /** @var string Company description */
    public $company_description;

    /** @var boolean Company regulations activity */
    public $regulations_active = false;

    /** @var string Company regulations */
    public $regulations;

    /** @var string Friendly URL */
    public $link_rewrite;

    public function __construct($id_seller = null, $id_customer = null)
    {
        if (empty($id_seller) && !empty($id_customer))
        {
            $query = new DbQuery();
            $query
                -> select('*')
                -> from('seller')
                -> where('`id_customer` = '.$id_customer);
            if ($result = Db::getInstance() -> executeS($query))
                $id_seller = $result[0]['id_seller'];
        }
        parent::__construct($id_seller);
        $this->image_dir = _NPS_SEL_IMG_DIR_;
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'seller',
        'primary' => 'id_seller',
        'multilang' => true,
        'fields' => array(
            'id_customer' =>         array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'request_date' =>        array('type' => self::TYPE_STRING, 'validate' => 'isDateFormat'),
            'active' =>              array('type' => self::TYPE_BOOL,   'validate' => 'isBool',        'required' => true),
            'requested' =>           array('type' => self::TYPE_BOOL,   'validate' => 'isBool',        'required' => true),
            'locked' =>              array('type' => self::TYPE_BOOL,   'validate' => 'isBool',        'required' => true),
            'email' =>               array('type' => self::TYPE_STRING, 'validate' => 'isEmail',       'required' => true),
            'phone' =>               array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'required' => true),
            'nip' =>                 array('type' => self::TYPE_STRING, 'validate' => 'isNip',         ),
            'regon' =>               array('type' => self::TYPE_STRING, 'validate' => 'isRegon',       ),
            'commision' =>           array('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt', 'required' => true),
            'regulations_active' =>  array('type' => self::TYPE_BOOL,   'validate' => 'isBool',        ),
             // Lang fields
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'required' => true, 'lang' => true, 'size' => 128),
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true, 'size' => 128),
            'company_description' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml',   'required' => false,'lang' => true),
            'company_name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true),
            'regulations' =>         array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml',   'required' => false,'lang' => true),
        ),
        'associations' => array(
            'customer' => array('type' => self::HAS_ONE,  'field' => 'id_customer', 'object' => 'Customer'),
            'products' => array('type' => self::HAS_MANY, 'field' => 'id_product',  'object' => 'Product', 'association' => 'seller_product'),
        )
    );

    public function delete() {
        $result = true;
        foreach ($this->getProducts() as $product) {
            $product->delete() ? $result = $result : $result = false;
        }
        if($result) {
            return parent::delete();
        }
        return $result;
    }

    /**
     * assignProduct assigns products to current seller.
     *
     * @param mixed $products id_product or array of id_product
     * @return boolean true if succeed
     */
    public function assignProduct($products = array())
    {
        if (!is_array($products))
            $products = array($products);

        if (count($products) < 0)
            return false;

        $products = array_map('intval', $products);

        $current_products = array_map('intval',$this->getSellerProducts($this->id));
        foreach ($current_products as $current_product)
            foreach ($products as $key => $value)
                if ($value == $current_product)
                    unset($products[$key]);

        if (count($products) < 0)
            return true;

        foreach ($products as $new_id_product)
            $seller_products[] = array(
                'id_product' => (int)$new_id_product,
                'id_seller' => (int)$this->id,
            );

        return Db::getInstance()->insert('seller_product', $seller_products);
    }

    public function getImageLink($type = null, $context)
    {
        if (file_exists(_NPS_SEL_IMG_DIR_.$this->id.'.'.$this->getImgFormat())) {
            if ($this->id) {
                if($type)
                    $uri_path = _THEME_SEL_DIR_.$this->id.'-'.$type.'.jpg';
                else
                    $uri_path = _THEME_SEL_DIR_.$this->id.($type ? '-'.$type : '').'.jpg';
                return $context->link->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
            }
        }
    }

    /**
     * getProducts return an array of products which this seller belongs to
     *
     * @return array of products objects
     */
    public function getProducts()
    {
        $products = array();
        $products_id = $this->getSellerProducts($this->id);
        foreach ($products_id as $product_id)
            $products[] = new Product($product_id);
        return $products;
    }

    /**
     * getSellerProducts return an array of products which this seller belongs to
     *
     * @return array of products
     */
    public static function getSellerProducts($id_seller = '')
    {
        $ret = array();

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_product` FROM `'._DB_PREFIX_.'seller_product`
            WHERE `id_seller` = '.(int)$id_seller
        );

        if ($row)
            foreach ($row as $val)
                $ret[] = $val['id_product'];

        return $ret;
    }

    /**
     * getSellerByProducts return seller id by products which this seller belongs to
     *
     * @return seller id
     */
    public static function getSellerByProduct($id_product = '')
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_seller`
            FROM `'._DB_PREFIX_.'seller_product`
            WHERE `id_product` = '.(int)$id_product);
    }

    /**
     * customerHasProduct checks if given product is assigned to customer
     *
     * @param id_seller seller id
     * @param id_product product id
     * @return boolean true if product is assigned to user
     */
    public static function sellerHasProduct($id_seller, $id_product)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_seller`
            FROM `'._DB_PREFIX_.'seller_product`
            WHERE `id_seller` = '.(int)$id_seller.'
            AND `id_product` = '.(int)$id_product);

        return isset($result);
    }

    public function getImgFormat() {
        return $this->image_format;
    }

    public function getAccountState() {
        if ($this->requested == 1 && $this->active == 0 && $this->locked == 0)
            return 'requested';
        else if ($this->requested == 1 && $this->active == 1 && $this->locked == 0)
            return 'active';
        else if ($this->requested == 1 && $this->locked == 1)
            return 'locked';
        return 'none';
    }
}

