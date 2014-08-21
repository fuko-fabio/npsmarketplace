{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Payment Settings' mod='npsprzelewy24'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Payment Settings' mod='npsprzelewy24'}</h1>
<div class="block-center" id="block-seller-payment-settings">
    <div class="alert alert-info">
        <p>{l s='Here short info why we register cumpany in Przelewy24 service' mod='npsprzelewy24'}</p>
    </div>
    {include file="$tpl_dir./errors.tpl"}
    <form role="form" action="{$request_uri}" method="post">
        <div class="form-group">
            <label class="required" for="spid">{l s='Seller Przelewy24 ID' mod='npsprzelewy24'}</label>
            <input class="validate form-control" data-validate="isNumber" type="number" id="spid" name="spid" required=""
            value="{if isset($smarty.post.spid)}{$smarty.post.spid}{else}{if isset($spid)}{$spid|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium" name="submitSpid" ><span>{l s='Save' mod='npsprzelewy24'}<i class="icon-save right"></i></span></button>
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