<?php
/*
 *  @author Norbert Pabian <norbert.pabian@gmail.com>
 *  @copyright 2014 npsoftware
 */

class Address extends AddressCore {

    /**
     * @see ObjectModel::delete()
     */
    public function delete() {
        if (Validate::isUnsignedId($this->id_customer))
            Customer::resetAddressCache($this->id_customer);

        if (!$this->isUsedBySeller())
            return parent::delete();
        else
            return false;
    }

        /**
     * Check if address is used (at least one order placed)
     *
     * @return integer Order count for this address
     */
    public function isUsedBySeller() {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT COUNT(`id_seller`) AS used
        FROM `'._DB_PREFIX_.'seller`
        WHERE `id_address` = '.(int)$this->id);

        return isset($result['used']) ? $result['used'] : false;
    }
}
