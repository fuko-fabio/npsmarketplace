<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
{capture name=path}{l s='Order confirmation' mod='npsprzelewy24'}{/capture}

<h1>{l s='Order confirmation' mod='npsprzelewy24'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

{if isset($error)}
    <div class="alert alert-danger">
        <p>{l s='Unable to finalize transaction.' mod='npsprzelewy24'}</p>
    </div>
    <p><b>{l s='Informations from Przelewy24:' mod='npsprzelewy24'}</b></p>
    <p><b>{l s='Error message:' mod='npsprzelewy24'}</b> {$error.message}</p>
    <p><b>{l s='Error code:' mod='npsprzelewy24'}</b> {$error.code}</p>
    <p><b>{l s='Please try to contact the customer support' mod='npsprzelewy24'}</b></p>
{/if}
{if isset($order)}
    <p>{l s='Total of the transaction:' mod='npsprzelewy24'} <span class="bold">{$price|escape:'htmlall':'UTF-8'}</span></p>
    <p>{l s='Your order ID is :' mod='npsprzelewy24'} 
        <span class="bold">
        {if isset($reference_order)}
            {$reference_order|escape:'htmlall':'UTF-8'}
        {else}
            {$order.id_order|intval}
        {/if}
        </span>
    </p>
    <p>{l s='Your Przelewy24 transaction ID is :' mod='npsprzelewy24'} <span class="bold">{$order.order_id|escape:'htmlall':'UTF-8'}</span></p>
{/if}
<br />
{if $is_guest}
    <p class="cart_navigation exclusive">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html':'UTF-8'}" title="{l s='Follow my order' mod='npsprzelewy24'}"><i class="icon-chevron-left"></i>{l s='Follow my order' mod='npsprzelewy24'}</a>
    </p>
{else}
    <p class="cart_navigation exclusive">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Back to orders' mod='npsprzelewy24'}"><i class="icon-chevron-left"></i>{l s='Back to orders' mod='npsprzelewy24'}</a>
    </p>
{/if}