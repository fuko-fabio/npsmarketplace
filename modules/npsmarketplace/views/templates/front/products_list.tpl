<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='My events' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-products-list">
    <h1 class="page-heading bottom-indent">{l s='My events' mod='npsmarketplace'}{if $seler_active}<a href="{$add_product_link}" class="btn btn-default pull-right"><i class="icon-plus"></i> {l s='Add Event' mod='npsmarketplace'}</a>{/if}</h1>

    {if $products}
    <div class="table-responsive">
        <table class="table table-bordered footab">
            <thead>
                <tr>
                    <th class="first_item" data-sort-ignore="true">{l s='Image' mod='npsmarketplace'}</th>
                    <th class="item">{l s='Name' mod='npsmarketplace'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Description' mod='npsmarketplace'}</th>
                    <th class="item">{l s='Price' mod='npsmarketplace'}</th>
                    <th class="item">{l s='Quantity' mod='npsmarketplace'}</th>
                    <th class="item">{l s='State' mod='npsmarketplace'}</th>
                    <th class="last_item" data-sort-ignore="true" width="150px"></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$products item=product}
                <tr>
                    <td>
                    {if $product.haveImage}
                        <img src="{$product['cover']}" class="imgm img-thumbnail" width="52"/>
                    {else}
                        <img src="{$img_prod_dir}{$lang_iso}-default-cart_default.jpg" class="imgm img-thumbnail" width="52"/>
                    {/if}
                    </td>
                    <td>{$product.name}</td>
                    <td>{$product.description}</td>
                    <td>{$product.price}</td>
                    <td>{$product.quantity}</td>
                    <td>
                        {if $product['active'] == 1}
                        <i class="icon-ok"></i>
                        {else}
                        <i class="icon-remove"></i>
                        {/if}
                    </td>
                    <td>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-list"></i> {l s='Options' mod='npsmarketplace'}<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{$product.edit_url}"> <i class="icon-pencil"></i> {l s='Edit' mod='npsmarketplace'}</a></li>
                                    {if $seler_active}
                                    <li><a href="{$product.new_combination_url}"> <i class="icon-calendar"></i> {l s='New Term' mod='npsmarketplace'}</a></li>
                                    {/if}
                                    {if $product.active == 1}
                                    <li><a href="{$product.edit_combination_url}"> <i class="icon-calendar"></i> {l s='List of Terms' mod='npsmarketplace'}</a></li>
                                    <li><a href="{$product.view_url}"> <i class="icon-search"></i> {l s='Preview' mod='npsmarketplace'}</a></li>
                                    {/if}
                                    <li class="divider"></li>
                                    <li><a href="{$product.delete_url}" class="delete"> <i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'}</a></li>
                                </ul>
                            </li>
                        </ul>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info">{l s='You have not added any event yet.' mod='npsmarketplace'}</p>
    {/if}
</div>