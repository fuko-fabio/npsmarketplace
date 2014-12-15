{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}{l s='Order confirmation' mod='npsprzelewy24'}{/capture}

<h1>{l s='Order confirmation' mod='npsprzelewy24'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

<p class="alert alert-success">
    <span class="alert-content">
        {l s='Transaction finalized successfully.' mod='npsprzelewy24'}
        {if isset($statement)}
            {l s='Payment has been accepted and confirmed by Przelewy 24.' mod='npsprzelewy24'}
        {else}
            {l s='Payment has been accepted but not confirmed by Przelewy 24. Soon we should get a confirmation and your tickets will be send.' mod='npsprzelewy24'}
        {/if}
    </span>
</p>

{if isset($statement)}
    <p>{l s='Your Przelewy24 transaction ID is :' mod='npsprzelewy24'} <span class="bold">{$statement}</span></p>
{/if}

{if isset($price) && isset($reference_order)}
    <p>{l s='Total of the transaction:' mod='npsprzelewy24'} <span class="bold">{displayPrice price=$price currency=$currency->id}</span></p>
    <p>{l s='Your order ID is :' mod='npsprzelewy24'} <a href="{$order_url}"><span class="bold">{$order_reference}</span></a></p>
{/if}
<br />
{if $is_guest}
    <p class="cart_navigation exclusive">
        <a class="btn btn-default button button-small " href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html':'UTF-8'}" title="{l s='Follow my order' mod='npsprzelewy24'}"><i class="icon-chevron-left"></i> {l s='Follow my order' mod='npsprzelewy24'}</a>
    </p>
{else}
    <p class="cart_navigation exclusive">
        <a class="btn btn-default button button-small " href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='Back to my tickets' mod='npsprzelewy24'}"><i class="icon-chevron-left"> </i>{l s='Back to my tickets' mod='npsprzelewy24'}</a>
    </p>
{/if}