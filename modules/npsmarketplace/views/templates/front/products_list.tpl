{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}

{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='My events' mod='npsmarketplace' mod='npsmarketplace'}</span>
{/capture}
<div class="block-center" id="block-seller-products-list">
    <h1 class="page-heading with-button">{l s='My events' mod='npsmarketplace'}{if $seler_active}<a href="{$add_product_link}" class="btn btn-default button button-small pull-right"><i class="icon-plus"></i> {l s='Add Event' mod='npsmarketplace'}</a>{/if}</h1>
    {include file="$tpl_dir./errors.tpl"}
    {if isset($not_found) && $not_found}
        <div class="alert alert-error">
            <p class="alert-content">{l s='Event not found. Please select existing item to edit.' mod='npsmarketplace'}</p>
        </div>
    {/if}
    {if isset($account_requested) && $account_requested}
        <div class="alert alert-info">
            <p class="alert-content">{l s='Your seller account is not active. Wait for response from our service.' mod='npsmarketplace'}</p>
        </div>
    {/if}

    {if $products}
    <div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix">
            {include file="$tpl_dir./nbr-product-page.tpl"}
        </div>
        <div class="top-pagination-content clearfix">
            {include file="$tpl_dir./pagination.tpl"}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered footab table-hover">
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
                    <td>{if !$product.advertisment }{displayPrice price=$product.price currency=$id_currency}{else}{l s='Advertisment' mod='npsmarketplace'}{/if}</td>
                    <td>{if !$product.advertisment }{$product.quantity}{/if}</td>
                    <td>
                        {if $product.active == 1}
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
                                    <li><a href="{$product.edit_url}" onclick="$.fancybox.showLoading();"> <i class="icon-pencil"></i> {l s='Edit' mod='npsmarketplace'}</a></li>
                                    {if $product.active == 1}
                                        {if !$product.advertisment }
                                            {if !$product.on_sale}
                                                <li><a class="sale-event-btn" href="#sale_event_box{$product.id_product}"> <i class="icon-chevron-down"></i> {l s='Sale' mod='npsmarketplace'}</a></li>
                                                <!-- Fancybox -->
                                                <div style="display:none">
                                                    <div id="sale_event_box{$product.id_product}" class="sale-event-box">
                                                        <h2 class="page-subheading">
                                                            {l s='Sale tickets!' mod='npsmarketplace'}
                                                        </h2>
                                                        <p id="sale_error_{$product.id_product}" class="alert alert-error" style="display:none;padding:15px 25px"></p>
                                                        <p class="alert alert-info"><span class="alert-content">{l s='Please enter price reduction value.' mod='npsmarketplace'}</span></p>
                                                        <div class="form-group">
                                                             <label class="required">{l s='Reduction' mod='npsmarketplace'}</label>
                                                            <input class="is_required validate form-control" data-validate="isPrice" type="text" id="reduction{$product.id_product}" required=""/>
                                                            <span class="form_info">{l s='Example: 10.50' mod='npsmarketplace'}</span>
                                                        </div>
                                                        <p class="submit">
                                                            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
                                                            <input class="button" name="submitSaleEvent" onclick="submitPriceReduction({$product.id_product}, $('#reduction{$product.id_product}').val());" value="{l s='Save' mod='npsmarketplace'}"/>
                                                        </p>
                                                    </div>
                                                </div>
                                                <!-- End fancybox -->
                                            {else}
                                                <li><a class="remove-sale-event-btn" href="#" onclick="removePriceReduction({$product.id_product})"><i class="icon-chevron-up"></i> {l s='Remove Sale' mod='npsmarketplace'}</a></li>
                                            {/if}
                                        {/if}
                                        <li><a href="{$product.view_url}" onclick="$.fancybox.showLoading();"> <i class="icon-search"></i> {l s='Preview' mod='npsmarketplace'}</a></li>
                                    {/if}
                                    <li class="divider"></li>
                                    <li><a href="{$product.delete_url}" class="delete" onclick="$.fancybox.showLoading();"> <i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'}</a></li>
                                </ul>
                            </li>
                        </ul>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
            {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
        </div>
    </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='You have not added any event yet.' mod='npsmarketplace'}</span></p>
    {/if}
</div>