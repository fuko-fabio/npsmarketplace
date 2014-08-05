<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once (_PS_MODULE_DIR_ . 'npsmarketplace/classes/Seller.php');

class NpsMarketplaceProductsListModuleFrontController extends ModuleFrontController {
    
    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('action') && Tools::isSubmit('id_product')) {
            if (Tools::getValue('action') == 'delete') {
                $p = new Product(Tools::getValue('id_product'));
                if ($p->delete())
                    Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
            } elseif (Tools::getValue('action') == 'view') {
                d('viev');
            }
        }
    }
    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this -> context -> customer -> id);
        $products = $this -> getProducts($seller);

        $this -> context -> smarty -> assign(array(
            'add_product_link' => $this -> context -> link -> getModuleLink('npsmarketplace', 'AddProduct'),
            'products' => $products));

        $this -> setTemplate('products_list.tpl');
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
                'view_url' => $this->context->link->getProductLink($product),
                'delete_url' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList', array('id_product' => $product->id, 'action' => 'delete')),
                'edit_url' => $this->context->link->getModuleLink('npsmarketplace', 'Product', array('id_product' => $product->id))
            );
        }
        return $result;
    }

}
?>