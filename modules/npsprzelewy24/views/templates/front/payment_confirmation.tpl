{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}{l s='Order confirmation' mod='npsprzelewy24'}{/capture}

<h1>{l s='Order confirmation' mod='npsprzelewy24'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}
{if !isset($errors)}
<p>{l s='You should be redirected to our partner Przelewy24 to securely pay by order. If not, click' mod='npsprzelewy24'} <a href="{$payment_url}">{l s='here' mod='npsprzelewy24'}</a></p>
    {if $is_guest}
        <p class="cart_navigation exclusive">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html':'UTF-8'}" title="{l s='Follow my order' mod='npsprzelewy24'}"><i class="icon-chevron-left"></i>{l s='Follow my order' mod='npsprzelewy24'}</a>
        </p>
    {else}
        <p class="cart_navigation exclusive">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Back to orders' mod='npsprzelewy24'}"><i class="icon-chevron-left"></i>{l s='Back to orders' mod='npsprzelewy24'}</a>
        </p>
    {/if}
{/if}