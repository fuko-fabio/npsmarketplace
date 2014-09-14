{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Comments' mod=npsmsellercomments}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Received Comments' mod=npsmsellercomments}</h1>
<div class="block-center" id="block-seller-account">
    <p class="alert alert-info">{l s='You have not received comments yet.' mod='npsmarketplace'}</p>
</div>