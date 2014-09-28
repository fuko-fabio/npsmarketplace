<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

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
}
?>