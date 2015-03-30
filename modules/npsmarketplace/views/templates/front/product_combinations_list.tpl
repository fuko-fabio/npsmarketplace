{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}

{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Event terms' mod='npsmarketplace'}</span>
{/capture}
<h1 class="page-heading with-button">{l s='%s Terms' sprintf=$name mod='npsmarketplace'}<a href="{$new_combination_url}" class="btn btn-default button button-small pull-right"><i class="icon-calendar"></i> {l s='Add Term' mod='npsmarketplace'}</a></h1>
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-products-list">
    {if $comb_array}
    <div class="table-responsive">
        <table class="table table-bordered footab">
            <thead>
                <tr>
                    <th class="first_item">{l s='Date' mod='npsmarketplace'}</th>
                    <th class="item">{l s='Hour' mod='npsmarketplace'}</th>
                    <th class="item">{l s='Quantity' mod='npsmarketplace'}</th>
                    <th class="last_item" data-sort-ignore="true" width="150px"></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$comb_array item=comb}
                <tr>
                    <td>{$comb.attributes[0][1]}</td>
                    <td>{$comb.attributes[1][1]}</td>
                    <td>{$comb.quantity}</td>
                    <td>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-list"></i> {l s='Options' mod='npsmarketplace'}<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="quantity-btn" href="#quantity_box{$comb.id_product_attribute}"> <i class="icon-pencil"></i> {l s='Edit quantity' mod='npsmarketplace'}</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li><a href="{$comb.delete_url}"><i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'}</a></i>
                                </ul>
                            </li>
                        </ul>
                    </td>
                </tr>
                <!-- Fancybox -->
                <div style="display:none">
                    <div id="quantity_box{$comb.id_product_attribute}" class="quantity-box">
                        <h2 class="page-subheading"> {l s='Edit quantity' mod='npsmarketplace'} </h2>
                        <p id="quantity_error_{$comb.id_product_attribute}" class="alert alert-error" style="display:none;">
                            <span class="alert-content">{l s='Unable to change quantity. Try again or contact with customer support.' mod='npsmarketplace'}</span>
                        </p>
                        <p id="quantity_validation_error_{$comb.id_product_attribute}" class="alert alert-error" style="display:none;">
                            <span class="alert-content">{l s='Quantity cannot be less than one.' mod='npsmarketplace'}</span>
                        </p>
                        <p class="alert alert-info">
                            <span class="alert-content">{l s='Please enter new quantity.' mod='npsmarketplace'}</span>
                        </p>
                        <div class="form-group">
                            <label class="required">{l s='Quantity' mod='npsmarketplace'}</label>
                            <input class="is_required validate form-control" data-validate="isQuantity" type="number" id="quantity{$comb.id_product_attribute}" required="" value="{$comb.quantity}"/>
                            <span class="form_info">{l s='Example: 15' mod='npsmarketplace'}</span>
                        </div>
                        <p class="submit">
                            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
                            <input class="button" name="submitSaleEvent" onclick="submitCombinationQuantity({$comb.id_product_attribute}, $('#quantity{$comb.id_product_attribute}').val());" value="{l s='Save' mod='npsmarketplace'}"/>
                        </p>
                    </div>
                </div>
                <!-- End fancybox -->
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='No available terms for %s.' sprintf=$name mod='npsmarketplace'}</span></p>
    {/if}
</div>