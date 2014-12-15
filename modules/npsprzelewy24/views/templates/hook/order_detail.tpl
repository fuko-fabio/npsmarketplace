{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<h1 class="page-heading">{l s='Payment' mod='npsprzelewy24'}</h1>
<div class="box">
    {if isset($p24_retryPaymentUrl)}
        <p>{l s='Your payment was canceled or not confirmed by Przelewy24.' mod='npsprzelewy24'}</p>
        <a class="btn btn-default button button-medium pull-right" href="{$p24_retryPaymentUrl}">{l s='Pay' mod='npsprzelewy24'} <i class="icon-money"></i></a>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='Payment has been accepted and confirmed by Przelewy 24.' mod='npsprzelewy24'}</span></p>
        <p>{l s='Your Przelewy24 transaction ID is :' mod='npsprzelewy24'} <span class="bold">{$statement}</span></p>
    {/if}
    <br />
</div>