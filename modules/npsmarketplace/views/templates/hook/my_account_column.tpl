<ul class="myaccount-link-list">
    {if $has_customer_an_address}
    <li><a href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Add my first address'}"><i class="icon-building"></i><span>{l s='Add my first address'}</span></a></li>
    {/if}
    <li><a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders'}"><i class="icon-list-ol"></i><span>{l s='Order history and details'}</span></a></li>
    {if $returnAllowed}
    <li><a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='Merchandise returns'}"><i class="icon-refresh"></i><span>{l s='My merchandise returns'}</span></a></li>
    {/if}
    <li><a href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='Credit slips'}"><i class="icon-ban-circle"></i><span>{l s='My credit slips'}</span></a></li>
    <li><a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='Addresses'}"><i class="icon-building"></i><span>{l s='My addresses'}</span></a></li>
    <li><a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Information'}"><i class="icon-user"></i><span>{l s='My personal information'}</span></a></li>
    {if $voucherAllowed || isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
    {if $voucherAllowed}
    <li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}"><i class="icon-barcode"></i><span>{l s='My vouchers'}</span></a></li>
    {/if}
    {$HOOK_CUSTOMER_ACCOUNT}
    {/if}
</ul>
