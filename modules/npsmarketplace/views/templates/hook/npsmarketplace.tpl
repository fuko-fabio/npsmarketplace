<ul>
    {if $account_state == 'none'}
        <li><a href="{$seller_request_link}"><i class="icon-share"></i><span>{l s='Become a seller' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'locked' || $account_state == 'active'}
        <li><a href="{$my_shop_profile_link}"><i class="icon-plus"></i><span>{l s='My shop profile' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'locked'}
        <li><a href="{$unlock_account_link}"><i class="icon-lock"></i><span>{l s='Seller account locked' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'active'}
        <li><a href="{$add_product_link}"><i class="icon-plus"></i><span>{l s='Add product' mod='npsmarketplace'}</span></a></li>
        <li><a href="{$products_list_link}"><i class="icon-th-list"></i><span>{l s='My products' mod='npsmarketplace'}</span></a></li>
        <li><a href="{$orders_link}"><i class="icon-gift"></i><span>{l s='List of sales' mod='npsmarketplace'}</span></a></li>
        <li><a href="{$payment_settings_link}"><i class="icon-money"></i><span>{l s='Payment settings' mod='npsmarketplace'}</span></a></li>
    {/if}
</ul>