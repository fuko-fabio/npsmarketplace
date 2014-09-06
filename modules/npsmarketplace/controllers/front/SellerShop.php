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
            $image = $seller->getImageLink('medium_default', $this->context);
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

        $this -> context -> smarty -> assign(array(
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'have_image' => $image != null,
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
        ));

        $this->setTemplate('seller_shop.tpl');
    }
}
