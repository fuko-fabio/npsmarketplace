{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Facebook authentication' mod='npsfacebooklogin'}</h1>
<div class="block-center" id="block-seller-account">
    <p class="alert alert-error">
        <span class="alert-content">
            {if $error == 0}
            {l s='Unable to authenticate via facebook account. Service temporarily unavailable or access denied.' mod='npsfacebooklogin'}
            {else}
            {l s='Unable to authenticate via facebook account. Required permisions not accepted by facebook user.' mod='npsfacebooklogin'}
            {/if}
        </span>
    </p>
</div>