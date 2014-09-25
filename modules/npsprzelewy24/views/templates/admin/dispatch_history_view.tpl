<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
<div class="panel">
    <div class="row">
        <div class="col-lg-12">
            <form class="container-command-top-spacing">
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-money"></i>
                        {l s='Dispatch History Details' mod=npsprzelewy24}
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dispatchHistoryDetails">
                            <thead>
                                <tr>
                                    <th><span class="title_box ">{l s='ID' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='Seller ID' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='SPID' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='Amount' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='Status' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='Error' mod=npsprzelewy24}</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$dispatch_history item=dh key=k}
                                {if !$dh['merchant']}
                                <tr class="product-line-row">
                                    <td>{$dh['id_p24_dispatch_history_detail']}</td>
                                    <td><a href="index.php?controller=AdminSellersAccounts&amp;updateseller&amp;id_seller={$dh['id_seller']}&amp;token={getAdminToken tab='AdminSellersAccounts'}"> <span>{$dh['id_seller']}</span>
                                    <td>{$dh['spid']}</td>
                                    <td class="total_product">{displayPrice price=$dh['amount']/100 currency=$currency->id}</td>
                                    <td>{if $dh['status']}<i class="icon-check"></i>{else}<i class="icon-remove"></i>{/if}</td>
                                    <td>{$dh['error']}</td>
                                </tr>
                                {/if}
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-total">
                    <div class="table-responsive">
                        {assign var=dh_total_calculated value=($history->sellers_amount + $history->merchant_amount + $history->p24_amount)}
                        {assign var=dh_to_dispatch value=($history->sellers_amount + $history->merchant_amount)}
                        <table class="table">
                            <tr id="dh_total_order">
                                <td class="text-right">{l s='Original total amount based on products price' mod=npsprzelewy24}:</td>
                                <td class="amount text-right">{displayPrice price=$history->total_amount/100 currency=$currency->id}</td>
                            </tr>
                            <tr id="dh_total_sellers">
                                <td class="text-right">{l s='Sellers' mod=npsprzelewy24}:</td>
                                <td class="amount text-right">{displayPrice price=$history->sellers_amount/100 currency=$currency->id} </td>
                            </tr>
                            <tr id="dh_total_merchant">
                                <td class="text-right">{l s='Merchant' mod=npsprzelewy24}:</td>
                                <td class="amount text-right">{displayPrice price=$history->merchant_amount/100 currency=$currency->id} </td>
                            </tr>
                            <tr id="dh_total_p24">
                                <td class="text-right">{l s='Przelewy24' mod=npsprzelewy24}:</td>
                                <td class="amount text-right">{displayPrice price=$history->p24_amount/100 currency=$currency->id} </td>
                            </tr>
                            <tr id="dh_total_order" class="{if $history->total_amount == $dh_total_calculated}info{else}danger{/if}">
                                <td class="text-right"><strong>{l s='Total after system calculations' mod=npsprzelewy24}:</strong></td>
                                <td class="amount text-right"><strong>{displayPrice price=$dh_total_calculated/100 currency=$currency->id}</strong></td>
                            </tr>
                            <tr id="dh_total_to_dispatch">
                                <td class="text-right">{l s='To dispatch without Przelewy24 commision' mod=npsprzelewy24}:</td>
                                <td class="amount text-right">{displayPrice price=$dh_to_dispatch/100 currency=$currency->id} </td>
                            </tr>
                            <tr id="dh_total_not_dispatched" class="{if $available_funds != 0}danger{/if}">
                                <td class="text-right"><strong>{l s='Bank account balance' mod=npsprzelewy24}:</strong></td>
                                <td class="amount text-right"><strong>{displayPrice price=$available_funds/100 currency=$currency->id}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="panel">
    <div class="row">
        <div class="col-lg-12">
            <form class="container-command-top-spacing">
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-shopping-cart"></i>
                        {l s='Products' mod=npsprzelewy24} <span class="badge">{$products|@count}</span>
                    </div>
                    {capture "TaxMethod"}
                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                    {l s='tax excluded.' mod=npsprzelewy24}
                    {else}
                    {l s='tax included.' mod=npsprzelewy24}
                    {/if}
                    {/capture}
                    <div class="table-responsive">
                        <table class="table" id="orderProducts">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><span class="title_box ">{l s='Product' mod=npsprzelewy24}</span></th>
                                    <th><span class="title_box ">{l s='Unit Price' mod=npsprzelewy24}</span><small class="text-muted">{$smarty.capture.TaxMethod}</small></th>
                                    <th class="text-center"><span class="title_box ">{l s='Qty'}</span></th>
                                    {if $stock_management}<th class="text-center"><span class="title_box ">{l s='Available quantity' mod=npsprzelewy24}</span></th>{/if} <th><span class="title_box ">{l s='Total'}</span><small class="text-muted">{$smarty.capture.TaxMethod}</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$products item=product key=k}
                                <tr class="product-line-row">
                                    <td>{if isset($product.image) && $product.image->id}{$product.image_tag}{/if}</td>
                                    <td><a href="index.php?controller=adminproducts&amp;id_product={$product['product_id']}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}"> <span class="productName">{$product['product_name']}</span>
                                    <br />
                                    {if $product.product_reference}{l s='Reference number:'} {$product.product_reference}
                                    <br />
                                    {/if}
                                    {if $product.product_supplier_reference}{l s='Supplier reference:'} {$product.product_supplier_reference}{/if} </a></td>
                                    <td> {* Assign product price *}
                                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                                    {assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
                                    {else}
                                    {assign var=product_price value=$product['unit_price_tax_incl']}
                                    {/if} <span class="product_price_show">{displayPrice price=$product_price currency=$currency->id}</span></td>
                                    <td class="productQuantity text-center"><span class="product_quantity_show{if (int)$product['product_quantity'] > 1} badge{/if}">{$product['product_quantity']}</span></td>
                                    {if $stock_management}<td class="productQuantity product_stock text-center">{$product['current_stock']}</td>{/if} <td class="total_product"> {displayPrice price=(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal'])) currency=$currency->id} </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-total">
                    <div class="table-responsive">
                        <table class="table">
                            {* Assign order price *}
                            {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                            {assign var=order_product_price value=($order->total_products)}
                            {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                            {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                            {else}
                            {assign var=order_product_price value=$order->total_products_wt}
                            {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                            {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                            {/if}
                            <tr id="total_products">
                                <td class="text-right">{l s='Products:' mod=npsprzelewy24}</td>
                                <td class="amount text-right"> {displayPrice price=$order_product_price currency=$currency->id} </td>
                                <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                            <tr id="total_discounts" {if $order->
                                total_discounts_tax_incl == 0}style="display: none;"{/if}> <td class="text-right">{l s='Discounts' mod=npsprzelewy24}</td>
                                <td class="amount text-right"> -{displayPrice price=$order_discount_price currency=$currency->id} </td>
                                <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                            <tr id="total_wrapping" {if $order->
                                total_wrapping_tax_incl == 0}style="display: none;"{/if}> <td class="text-right">{l s='Wrapping' mod=npsprzelewy24}</td>
                                <td class="amount text-right"> {displayPrice price=$order_wrapping_price currency=$currency->id} </td>
                                <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                            <tr id="total_shipping">
                                <td class="text-right">{l s='Shipping' mod=npsprzelewy24}</td>
                                <td class="amount text-right" > {displayPrice price=$order_shipping_price currency=$currency->id} </td>
                                <td class="partial_refund_fields current-edit" style="display:none;">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        {$currency->prefix}
                                        {$currency->suffix}
                                    </div>
                                    <input type="text" name="partialRefundShippingCost" value="0" />
                                </div></td>
                            </tr>
                            {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                            <tr id="total_taxes">
                                <td class="text-right">{l s='Taxes' mod=npsprzelewy24}</td>
                                <td class="amount text-right" >{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$currency->id}</td>
                                <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                            {/if}
                            {assign var=order_total_price value=$order->total_paid_tax_incl}
                            <tr id="total_order">
                                <td class="text-right"><strong>{l s='Total' mod=npsprzelewy24}</strong></td>
                                <td class="amount text-right"><strong>{displayPrice price=$order_total_price currency=$currency->id}</strong></td>
                                <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
