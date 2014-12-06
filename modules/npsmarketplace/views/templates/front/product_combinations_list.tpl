{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
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
                    <td><a href="{$comb.delete_url}" class="btn btn-default"><i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'}</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='No available terms for %s.' sprintf=$name mod='npsmarketplace'}</span></p>
    {/if}
</div>