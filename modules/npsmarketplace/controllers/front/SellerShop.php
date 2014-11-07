<?php
/*
*  @author Norbert Pabian
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceSellerShopModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $id_seller = (int)Tools::getValue('id_seller', 0);
        $tpl_seller = array();
        if ($id_seller) {
            $seller = new Seller($id_seller);
            $image = Seller::getImageLink($seller->id, 'medium_default', $this->context);
            $tpl_seller = array(
                'id' => $seller->id,
                'image' => $image,
                'name' => $seller->name,
                'company_name' => $seller->company_name,
                'company_description' => $seller->company_description,
                'phone' => $seller->phone,
                'email' => $seller->email,
                'nip' => $seller->nip,
                'regon' => $seller->regon,
                'active' => $seller->active,
                'request_date' => $seller->request_date,
                'commision' => $seller->commision,
                'account_state' => $seller->getAccountState(),
                'regulations' => $seller->regulations,
                'regulations_active' => $seller->regulations_active,
            );
        }

        $this->productSort();

        $ids = Seller::getSellerProducts($id_seller);
        $this->pagination($this->productsCount($ids));

        $this -> context -> smarty -> assign(array(
            'HOOK_SELLER_TAB' => Hook::exec('sellerTab'),
            'HOOK_SELLER_TAB_CONTENT' => Hook::exec('sellerTabContent'),
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'have_image' => $image != null,
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
            'products' =>  $this->productsList($ids),
            'comments' => $this->commentsList($id_seller),
        ));

        $this->setTemplate('seller_shop.tpl');
    }

    private function productsList($ids) {
        if (empty($ids))
            return array();
        else
            return Product::getProductsByIds(
                $this->context->language->id,
                $ids,
                (isset($this->p) ? (int)($this->p) - 1 : null),
                (isset($this->n) ? (int)($this->n) : null),
                false,
                $this->orderBy,
                $this->orderWay,
                $this->context
            );
            
    }

    private function productsCount($ids) {
        if (empty($ids))
            return 0;
        else
            return (int)Product::getProductsByIds(
                $this->context->language->id,
                $ids,
                (isset($this->p) ? (int)($this->p) - 1 : null),
                (isset($this->n) ? (int)($this->n) : null),
                true
            );
    }

    private function commentsList($id_seller) {
    }
}
