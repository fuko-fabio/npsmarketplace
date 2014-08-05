{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Unlock Account'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Unlock Account'}</h1>
<div class="block-center" id="block-seller-account">
    <form action="{$request_uri}" method="post">
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Send' mod='npsmarketplace'}<i class="icon-send right"></i></span></button>
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