<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class SellerReportDataCollector {

    private $start_date;
    private $end_date;
    private $seller;
    private $customer;
    private $address;

    public function __construct(Seller $seller, $start_date, $end_date) {
        $this->seller = $seller;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->customer = new Customer($this->seller->id_customer);
        $this->address = new Address($this->seller->id_address);
    }

    public function collect() {
        $rows = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'seller_invoice_data`
            WHERE (date BETWEEN \''.$this->start_date.'\' AND \''.$this->end_date.'\') AND `id_seller` = '.(int)$this->seller->id
        );
        $result = array();
        foreach ($rows as $data) {
            $p = new Product($data['id_product']);
            $qty = $data['product_qty'];
            $total = $data['product_total_price'] / 100;
            $commission = $data['commission'] / 100;
            $result[] = array(
                'id_currency' => $data['id_currency'],
                'date' => $data['date'],
                'product_name' => $p->name[(int)Configuration::get('PS_LANG_DEFAULT')],
                'product_reference' => $p->reference,
                'unit_price' => $total / $qty,
                'product_quantity' => $qty,
                'total_price' => $total,
                'commision_price' => $commission,
                'seller_price' => $total - $commission
            );
        }
        $parts = explode(' ', $this->start_date);
        $s_date = $parts[0];
        $parts = explode(' ', $this->end_date);
        $e_date = $parts[0];
        return array(
            'items' => $result,
            'total_commison' => $this->count($result, 'commision_price'),
            'total_seller' => $this->count($result, 'seller_price'),
            'total' => $this->count($result, 'total_price'),
            'start_date' => $s_date,
            'end_date' => $e_date,
            'id_seller' => $this->seller->id,
            'company_name' => $this->address->company,
            'empty' => count($rows) == 0
        );
    }


    private function count($items, $attr) {
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item[$attr];
        }
        return $sum;
    }
}
