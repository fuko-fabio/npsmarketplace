<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Unlock Account'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Unlock Account'}</h1>
<div class="block-center" id="block-seller-account">
    <form action="{$request_uri}" method="post">
        <button type="submit" class="btn btn-primary btn-lg pull-right"><span>{l s='Send' mod='npsmarketplace'} <i class="icon-send right"></i></span></button>
    </form>
</div>