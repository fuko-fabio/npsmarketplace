{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Shop Profile'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Shop Profile' mod='npsmarketplace'}</h1>
{if $seller['account_state'] == 'requested'}
<p class="alert alert-info">
    {l s='Your account is not activated. Please wait for contact with our administrator. Request has been sent on %s' sprintf=$seller['request_date'] mod='npsmarketplace'}
</p>
{/if}
{if $seller['account_state'] == 'locked'}
<p class="alert alert-warning">
    {l s='Your account has been locked.' mod='npsmarketplace'}
</p>
{/if}
<div class="block-center" id="block-seller-account">
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="selleraccount">
        {include file="$seller_fieldset_tpl_path"}
        <button type="submit" class="btn btn-primary btn-lg pull-right"><span>{l s='Save' mod='npsmarketplace'} <i class="icon-save right"></i></span></button>
    </form>
</div>
