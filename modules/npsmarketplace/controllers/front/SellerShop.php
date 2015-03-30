<?php
/*
*  @author Norbert Pabian
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceSellerShopModuleFrontController extends ModuleFrontController {

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        $id_seller = (int)Tools::getValue('id_seller', 0);
        $tpl_seller = array();
        $seller = new Seller($id_seller);
        $address = new Address($seller->id_address);
        $customer = new Customer($seller->id_customer);
        if ($id_seller) {
            $image = Seller::getImageLink($seller->id, 'home_default', $this->context);
            $tpl_seller = array(
                'id' => $seller->id,
                'image' => $image,
                'name' => $seller->name,
                'description' => $seller->description,
                'company' => $address->company,
                'person' => $address->firstname.' '.$address->lastname,
                'address' => $address->address1.' '.$address->address2,
                'phone' => $address->phone,
                'mobilephone' => $address->phone_mobile, 
                'email' => $customer->email,
                'nip' => $seller->nip,
                'krs' => $seller->krs,
                'krs_reg' => $seller->krs_reg,
                'regon' => $seller->regon,
                'active' => $seller->active,
                'request_date' => $seller->request_date,
                'commision' => $seller->commision,
                'account_state' => $seller->getAccountState(),
                'regulations' => $seller->regulations,
            );
        }

        $this->productSort();
        $ids = Seller::getSellerProducts($id_seller, 0, 0, true);
        $this->pagination(count($ids));

        $this->context->smarty->assign(array(
            'HOOK_SELLER_TAB' => Hook::exec('sellerTab'),
            'HOOK_SELLER_TAB_CONTENT' => Hook::exec('sellerTabContent', array('seller' => $seller)),
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'have_image' => $image != null,
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
            'products' =>  $this->productsList($id_seller),
        ));

        $this->setTemplate('seller_shop.tpl');
    }

    private function productsList($id_seller) {
        $ids = Seller::getSellerProducts($id_seller, ((int)($this->p) - 1) * (int)($this->n), (int)($this->n), true);
        return Product::getProductsByIds(
            $this->context->language->id,
            $ids,
            null,
            null,
            false,
            $this->orderBy,
            $this->orderWay,
            $this->context
        );
    }
}
