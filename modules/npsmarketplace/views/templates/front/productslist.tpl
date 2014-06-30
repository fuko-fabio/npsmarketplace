{capture name=path} <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='My products'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="box">
    <h1 class="page-heading bottom-indent">{l s='My products'}</h1>

    {if $products}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="active">
                    <td>Image</td>
                    <td>Name</td>
                    <td>Description</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Status</td>
                    <td>Action</td>
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
                    <td>{$product['active']}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="#" title="Edytuj" class="edit btn btn-default"><i class="icon-pencil"></i> Edit</a>
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" title="Skopiuj"> <i class="icon-copy"></i> Preview </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" title="Usuń" class="delete"> <i class="icon-trash"></i> Delete </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info">{l s='You have not added any product yet.' mod='npsmarketplace'}</p>
        </br>
        {l s='Click' mod='npsmarketplace'} <a href="{$add_product_link}">{l s='here'}</a> {l s='to add your first product.' mod='npsmarketplace'}
    {/if}
</div>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> <span> <i class="icon-chevron-left"></i> {l s='Back to Your Account'} </span> </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}"> <span><i class="icon-chevron-left"></i> {l s='Home'}</span> </a>
    </li>
</ul>