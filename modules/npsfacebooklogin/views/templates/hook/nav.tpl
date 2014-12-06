{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<ul class="nav nav-pills nps-facebook-login pull-right">
    {if $logged}
    <li class="dropdown pull-right">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle head-item">
            {if isset($fb_img_url)}
            <img src="{$fb_img_url}" />
            {else}
            <i class="icon-user"></i>
            {/if}
            {$cookie->customer_firstname} {$cookie->customer_lastname}
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{$link->getPageLink('my-account', true)|escape:'html'}"><i class="icon-ticket"></i> {l s='My tickets' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getModuleLink('npsfavorite', 'account')|escape:'html':'UTF-8'}" title="{l s='My favorite products.' mod='npsfacebooklogin'}"><i class="icon-heart"></i> {l s='My favorite products' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getPageLink('identity', true)|escape:'html'}"><i class="icon-ticket"></i> {l s='Settings' mod='npsfacebooklogin'}</a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{$link->getModuleLink('npsmarketplace', 'Product')|escape:'html'}"><i class="icon-dollar"></i> {l s='Sell' mod='npsfacebooklogin'}</a>
            </li>

            <li class="divider"></li>
            <li>
                <a href="{$link->getPageLink('index', true, NULL, 'mylogout')|escape:'html'}"><i class="icon-signout"></i> {l s='Log out' mod='npsfacebooklogin'}</a>
            </li>
        </ul>
    </li>
    {else}
    <li class="dropdown pull-right">
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}" class="head-item login"><i class="icon-signin"></i> {l s='Log in' mod='npsfacebooklogin'}</a>
    </li>
    {/if}
</ul>