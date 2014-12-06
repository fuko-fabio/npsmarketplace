{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Become a seller' mod='npsmarketplace'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Become a seller' mod='npsmarketplace'}</h1>
{include file="$tpl_dir./errors.tpl"}
{if $account_state == 'requested'}
<p class="info-title">
    {l s='Your request has been sent to us on %s. Please wait for contact with our marketing team.' sprintf=$account_request_date mod='npsmarketplace'}
</p>
{else if $account_state == 'locked'}
<p class="info-title">
    {l s='Your account has been locked by administrator'}
</p>
{/if}
{if $account_state == 'none'}
{if isset($add_product) && $add_product}
    <div class="alert alert-info">
        <p class="alert-content">{l s='To be able add event you need to register seller account.' mod='npsprzelewy24'}</p>
    </div>
{/if}
<div class="block-center" id="block-seller-account">
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="formaccountrequest">
        {include file="$seller_fieldset_tpl_path"}
        <label class="required">{l s='Required field' mod='npsmarketplace'}</label>
        <br />
        <strong>{l s='By clicking "Submit" I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsmarketplace'}</a></strong>
        <br />
        <button type="submit" class="btn btn-default button button-medium pull-right" name="submitSeller"><span>{l s='Submit' mod='npsmarketplace'} <i class="icon-share right"></i></span></button>
    </form>
</div>
{/if}
