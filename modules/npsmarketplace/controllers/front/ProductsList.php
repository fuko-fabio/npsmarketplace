<?php
/*
 *  @author Norbert Pabian
 *  @copyright
 *  @license
 */

include_once (_PS_MODULE_DIR_ . 'npsmarketplace/classes/Seller.php');

class NpsMarketplaceProductsListModuleFrontController extends ModuleFrontController {
    
    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
    }
    
    public function initContent() {
        parent::initContent();

        $seller = new SellerCore(null, $this -> context -> customer -> id);
        $products = $this -> getProducts($seller);

        $this -> context -> smarty -> assign(array(
            'add_product_link' => $this -> context -> link -> getModuleLink('npsmarketplace', 'AddProduct'),
            'products' => $products));

        $this -> setTemplate('ProductsList.tpl');
    }

    private function getProducts($seller = null) {
        $result = array();
        $products = $seller -> getProducts();
        foreach ($products as $product) {
            $link = new Link();
            $cover = Product::getCover($product->id);
            $result[] = array(
                'cover' => $link->getImageLink($product->link_rewrite, $cover['id_image'], 'cart_default'),
                'name' => Product::getProductName($product->id),
                'description' => $product->description_short[$this->context->language->id],
                'price' => $product->getPrice(),
                'quantity' => Product::getQuantity($product->id),
                'active' => $product->active,
                'edit_url' => $this->context->link->getModuleLink('npsmarketplace', 'Product', array('id_product' => $product->id))
            );
        }
        return $result;
    }

}
?>