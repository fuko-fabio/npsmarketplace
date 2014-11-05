<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/npsmarketplace.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

$module = new NpsMarketplace();
if (Tools::getValue('action') == 'sendToSeller' && Tools::getValue('secure_key') == $module->secure_key) {
    $question = Tools::getValue('question');
    $email = Tools::getValue('email');
    $id_product = Tools::getValue('id_product');

    if (!$question || !$email || !$id_product)
        die('0');

    $ctx = Context::getContext();
    /* Email generation */
    $product = new Product((int)$id_product, false, $ctx->language->id);
    $productLink = $ctx->link->getProductLink($product);

    $seller = new Seller(Seller::getSellerByProduct($id_product));
    $templateVars = array(
        '{seller_name}' => $seller->name,
        '{product_name}' => $product->name,
        '{product_link}' => $productLink,
        '{question}' => Tools::safeOutput($question),
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
    );

    /* Email sending */
    if (!Mail::Send((int)$ctx->language->id,
            'question_to_seller',
            sprintf(Mail::l('Question about %1$s', (int)$ctx->language->id), $product->name),
            $templateVars,
            $seller->email,
            $seller->name,
            $email,
            ($ctx->cookie->customer_firstname ? $ctx->cookie->customer_firstname.' '.$ctx->cookie->customer_lastname : null),
            null,
            null,
            dirname(__FILE__).'/mails/'))
        die('0');
    die('1');
}
die('0');
