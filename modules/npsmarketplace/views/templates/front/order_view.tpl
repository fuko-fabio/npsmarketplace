{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Order Details' mod='npsmarketplace'}</span>
{/capture}
<div class="block-center" id="block-seller-order-view">
    <h1 class="page-heading bottom-indent">{l s='Order Details' mod='npsmarketplace'}</h1>
    {include file="$tpl_dir./errors.tpl"}
    <div class="row">
        <div class="col-md-6">
            <h4>{l s='State' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th scope="row">{l s='State' mod='npsmarketplace'}</th>
                        <td>{$order['state']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Payment Method' mod='npsmarketplace'}</th>
                        <td>{$order['payment']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Date & Time' mod='npsmarketplace'}</th>
                        <td>{$order['date_add']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Price' mod='npsmarketplace'}</th>
                        <td>{displayPrice price=$order['price'] currency=$currency->id}</td>
                    </tr>
                </table>
            </div>
            </br>
            <h4>{l s='Customer' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th scope="row">{l s='Firstname' mod='npsmarketplace'}</th>
                        <td>{$customer['firstname']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Lastname' mod='npsmarketplace'}</th>
                        <td>{$customer['lastname']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Email' mod='npsmarketplace'}</th>
                        <td><a href="mailto:{$customer['email']}"><i class="icon-envelope"></i> {$customer['email']}</a></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h4>{l s='Delivery Address' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td>
                            {$address['firstname']} {$address['lastname']}</br>
                            {if $address['address1']}{$address['address1']}</br>{/if}
                            {if $address['address2']}{$address['address2']}</br>{/if}
                            {$address['postcode']} {$address['city']}</br>
                            {if $address['phone']}<a href="tel:{$address['phone']}"><i class="icon-phone"></i>  {$address['phone']}</a></br>{/if}
                            {if $address['phone_mobile']}<a href="tel:{$address['phone_mobile']}"><i class="icon-phone"></i>  {$address['phone_mobile']}</a></br>{/if}
                            </br>
                            <div id="map_canvas" style="min-height: 250px;" data-target="{$address['address1']} {$address['address2']}"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4>{l s='Products' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>{l s='Image' mod='npsmarketplace'}</td>
                            <td>{l s='Name' mod='npsmarketplace'}</td>
                            <td>{l s='Unit Price' mod='npsmarketplace'}</td>
                            <td>{l s='Quantity' mod='npsmarketplace'}</td>
                            <td>{l s='Total Price' mod='npsmarketplace'}</td>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$products item=product}
                        <tr>
                            <td>
                            {if $product['has_image']}
                                <img src="{$product['cover']}" class="imgm img-thumbnail" width="52"/>
                            {else}
                                <img src="{$img_prod_dir}{$lang_iso}-default-cart_default.jpg" class="imgm img-thumbnail" width="52"/>
                            {/if}
                            </td>
                            <td>{$product['product_name']}</td>
                            <td>{displayPrice price=$product['unit_price_tax_incl'] currency=$currency->id}</td>
                            <td>{$product['product_quantity']}</td>
                            <td>{displayPrice price=$product['total_price_tax_incl'] currency=$currency->id}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {$HOOK_ORDERDETAILDISPLAYED}
</div>