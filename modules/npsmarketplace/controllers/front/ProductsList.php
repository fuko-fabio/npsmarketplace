<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once (_PS_MODULE_DIR_ . 'npsmarketplace/classes/Seller.php');

class NpsMarketplaceProductsListModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
        $this->addJqueryPlugin('scrollTo');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/product_list.js');
        $this->addCSS (_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('action') && Tools::isSubmit('id_product')) {
            if (Tools::getValue('action') == 'delete') {
                $p = new Product(Tools::getValue('id_product'));
                if ($p->delete())
                    Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
            }
        }
    }

    public function init() {
        $this->page_name = 'products-list';
        parent::init();
    }

    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $products = $this -> getProducts($seller);
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'add_product_link' => $this -> context -> link -> getModuleLink('npsmarketplace', 'Product'),
            'products' => $products,
            'seler_active' => $seller->active && !$seller->locked,
            'id_currency' => $this->context->currency->id,
            'nps_ajax_url' => $this->context->link->getModuleLink('npsmarketplace', 'Ajax'),
            'account_requested' => Tools::getValue('not_configured'),
            'not_found' => Tools::getValue('not_found'),
        ));

        $this -> setTemplate('products_list.tpl');
    }

    private function getProducts($seller) {
        $result = array();
        $products = $seller->getProducts();
        if (empty($products))
            return $result;
        foreach ($products as $product) {
            $cover = Product::getCover($product->id);
            $have_image = !empty($cover);
            $item = array(
                'id_product' => $product->id,
                'haveImage' => $have_image,
                'cover' => $have_image ? $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $cover['id_image'], 'cart_default') : null,
                'name' => Product::getProductName($product->id),
                'description' => $product->description_short[$this->context->language->id],
                'price' => $product->getPrice(),
                'quantity' => Product::getQuantity($product->id),
                'active' => $product->active,
                'view_url' => $this->context->link->getProductLink($product),
                'delete_url' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList', array('id_product' => $product->id, 'action' => 'delete')),
                'edit_url' => $this->context->link->getModuleLink('npsmarketplace', 'Product', array('id_product' => $product->id)),
                'new_combination_url' => $this->context->link->getModuleLink('npsmarketplace', 'ProductCombination', array('id_product' => $product->id)),
                'edit_combination_url' => $this->context->link->getModuleLink('npsmarketplace', 'ProductCombinationList', array('id_product' => $product->id)),
            );
            $extras = Product::getExtras($product->id, $this->context->language->id);
            if (!empty($extras)) 
                $item = array_merge($item, $extras);
            $sp = SpecificPrice::getIdsByProductId($product->id);
            if ($sp && !empty($sp))
                $item['on_sale'] = 1;
            else
                $item['on_sale'] = 0;
            $result[] = $item;
        }
        return $result;
    }

}
?>