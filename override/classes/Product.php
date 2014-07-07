<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
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