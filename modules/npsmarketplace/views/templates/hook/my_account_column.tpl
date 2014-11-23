{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<ul class="nps-myaccount-block">
    <li class="section"><p>{l s='Shopping' mod='npsmarketplace'}</p></li>
    <li><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My Tickets' mod='npsmarketplace'}"><i class="icon-ticket"></i><span>{l s='My Tickets' mod='npsmarketplace'}</span></a></li>
    <li><a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Information' mod='npsmarketplace'}"><i class="icon-user"></i><span>{l s='My account' mod='npsmarketplace'}</span></a></li>
    {if $has_customer_an_address}
    <li><a href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Add my first address' mod='npsmarketplace'}"><i class="icon-building"></i><span>{l s='Add address' mod='npsmarketplace'}</span></a></li>
    {/if}
    <li><a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='Addresses' mod='npsmarketplace'}"><i class="icon-building"></i><span>{l s='My addresses' mod='npsmarketplace'}</span></a></li>
    <li><a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders' mod='npsmarketplace'}"><i class="icon-list-ol"></i><span>{l s='Order history' mod='npsmarketplace'}</span></a></li>
    {if $voucherAllowed || isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
    {if $voucherAllowed}
    <li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers' mod='npsmarketplace'}"><i class="icon-barcode"></i><span>{l s='My vouchers' mod='npsmarketplace'}</span></a></li>
    {/if}
    {$HOOK_CUSTOMER_ACCOUNT}
    {/if}
</ul>
