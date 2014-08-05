{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
{if isset($product['id'])}
<span class="navigation_page">{l s='Edit Event'}</span>
{else}
<span class="navigation_page">{l s='Add Event'}</span>
{/if}
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($product['id'])}
<h1 class="page-heading bottom-indent">{l s='Edit Event' mod='npsmarketplace'}</h1>
{else}
<h1 class="page-heading bottom-indent">{l s='Add Event' mod='npsmarketplace'}</h1>
{/if}
<div class="block-center" id="block-seller-product">
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="formaddproduct">
        {include file="$product_fieldset_tpl_path" categories_tree=$categories_tree category_partial_tpl_path=$category_partial_tpl_path}
        </br>
        <label class="required">{l s='Required field' mod='npsmarketplace'}</label>
        </br>
        <strong>{l s='By clicking "Add" I agree that:' mod='npsmarketplace'}</strong>
        <ul>
            <li>{l s='I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.'}</a></li>
        </ul>
        </br>
        <p class="submit">
            {if isset($product['id'])}
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Save' mod='npsmarketplace'}<i class="icon-save right"></i></span></button>
            {else}
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Add' mod='npsmarketplace'}<i class="icon-plus right"></i></span></button>
            {/if}
        </p>
    </form>
</div>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> <span> <i class="icon-chevron-left"></i> {l s='Back to Your Account'} </span> </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}"> <span><i class="icon-chevron-left"></i> {l s='Home'}</span> </a>
    </li>
</ul>