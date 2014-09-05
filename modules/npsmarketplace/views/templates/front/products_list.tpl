{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='My products'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='My products'}</h1>

<div class="block-center" id="block-seller-products-list">
    {if $products}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="active">
                    <td>{l s='Image' mod='npsmarketplace'}</td>
                    <td width="120px" >{l s='Name' mod='npsmarketplace'}</td>
                    <td>{l s='Description' mod='npsmarketplace'}</td>
                    <td width="60px">{l s='Price' mod='npsmarketplace'}</td>
                    <td width="60px">{l s='Quantity' mod='npsmarketplace'}</td>
                    <td width="60px">{l s='State' mod='npsmarketplace'}</td>
                    <td width="120px">{l s='Action' mod='npsmarketplace'}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$products item=product}
                <tr class="active">
                    <td><img src="{$product['cover']}" class="imgm img-thumbnail" width="52"/></td>
                    <td>{$product['name']}</td>
                    <td>{$product['description']}</td>
                    <td>{$product['price']}</td>
                    <td>{$product['quantity']}</td>
                    <td>
                        {if $product['active'] == 1}
                        <i class="icon-ok"></i>
                        {else}
                        <i class="icon-remove"></i>
                        {/if}
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{$product.edit_url}" class="edit btn btn-default"><i class="icon-pencil"></i> {l s='Edit' mod='npsmarketplace'}</a>
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {if $product['active'] == 1}
                                <li>
                                    <a href="{$product.view_url}"> <i class="icon-search"></i> {l s='Preview' mod='npsmarketplace'} </a>
                                </li>
                                <li>
                                    <a href="{$product.new_combination_url}"> <i class="icon-calendar"></i> {l s='New Term' mod='npsmarketplace'} </a>
                                </li>
                                <li>
                                    <a href="{$product.edit_combination_url}"> <i class="icon-calendar"></i> {l s='Edit Terms' mod='npsmarketplace'} </a>
                                </li>
                                <li class="divider"></li>
                                {/if}
                                <li>
                                    <a href="{$product.delete_url}" class="delete"> <i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'} </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <script>
            $('.dropdown-toggle').dropdown();
        </script>
    </div>
    {else}
        <p class="alert alert-info">{l s='You have not added any product yet.' mod='npsmarketplace'}</p>
        </br>
        {l s='Click' mod='npsmarketplace'} <a href="{$add_product_link}">{l s='here'}</a> {l s='to add your first product.' mod='npsmarketplace'}
    {/if}
</div>