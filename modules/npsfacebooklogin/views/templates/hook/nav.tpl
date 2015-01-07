{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<script>
$(document).ready(function(){
    $('.nps-facebook-login').hover(function() {
      $(this).find('.dropdown-menu').stop(true, true).delay(0).fadeIn(200);
    }, function() {
      $(this).find('.dropdown-menu').stop(true, true).delay(400).fadeOut(200);
    });
});
</script>
<ul class="nav nav-pills nps-facebook-login pull-right">
    {if $logged}
    <li class="dropdown pull-right">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle head-item">
            {if isset($fb_img_url) && !empty($fb_img_url)}
            <img src="{$fb_img_url}" />
            {else}
            <i class="icon-user"></i>
            {/if}
            {$cookie->customer_firstname} {$cookie->customer_lastname}
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{$link->getPageLink('my-account', true)|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-ticket"></i> {l s='My tickets' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getModuleLink('npsfavorite', 'account')|escape:'html':'UTF-8'}" onclick="$.fancybox.showLoading();" title="{l s='My favorite products.' mod='npsfacebooklogin'}"><i class="icon-heart"></i> {l s='My favorite products' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getPageLink('identity', true)|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-user"></i> {l s='Settings' mod='npsfacebooklogin'}</a>
            </li>
            {if $is_seller}
            <li class="divider"></li>
            <li>
                <a href="{$link->getModuleLink('npsmarketplace', 'Product')|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-dollar"></i> {l s='Sell' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getModuleLink('npsmarketplace', 'ProductsList')|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-th-list"></i> {l s='My events' mod='npsfacebooklogin'}</a>
            </li>
            <li>
                <a href="{$link->getModuleLink('npsticketdelivery', 'TicketsSold')|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-ticket"></i> {l s='Sold tickets' mod='npsfacebooklogin'}</a>
            </li>
            {/if}
            <li class="divider"></li>
            <li>
                <a href="{$link->getPageLink('index', true, NULL, 'mylogout')|escape:'html'}" onclick="$.fancybox.showLoading();"><i class="icon-signout"></i> {l s='Log out' mod='npsfacebooklogin'}</a>
            </li>
        </ul>
    </li>
    {else}
    <li class="dropdown pull-right">
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}" class="head-item login" onclick="$.fancybox.showLoading();"><i class="icon-signin"></i> {l s='Log in' mod='npsfacebooklogin'}</a>
    </li>
    {/if}
</ul>
