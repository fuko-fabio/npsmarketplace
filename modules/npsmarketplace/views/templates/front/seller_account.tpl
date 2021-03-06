{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Shop Profile' mod='npsmarketplace'}</span>
{/capture}
<h1 class="page-heading with-button">{l s='Shop Profile' mod='npsmarketplace'}<a href="{$my_shop_link}" onclick="$.fancybox.showLoading();" class="btn btn-default button button-small pull-right"><i class="icon-search"></i> {l s='Shop preview' mod='npsmarketplace'}</a></h1>

{include file="$tpl_dir./errors.tpl"}

{if $seller['account_state'] == 'requested'}
<p class="alert alert-info">
    <span class="alert-content">
    {l s='Your account is not activated. Please wait for contact with our administrator. Request has been sent on %s' sprintf=$seller['request_date'] mod='npsmarketplace'}
    </span>
</p>
{/if}
{if $seller['account_state'] == 'locked'}
<p class="alert alert-warning">
    <span class="alert-content">
    {l s='Your account has been locked.' mod='npsmarketplace'}
    </span>
</p>
{/if}
<div class="block-center" id="block-seller-account">
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="selleraccount">
        {include file="$seller_fieldset_tpl_path"}
        <button type="submit" class="btn btn-default button button-medium pull-right" name="submitSeller"><span>{l s='Save' mod='npsmarketplace'} <i class="icon-save right"></i></span></button>
    </form>
</div>
