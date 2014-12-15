{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<li><a {if $page_name == 'payment-settings'}class="active"{/if} href="{$payment_settings_link}" onclick="$.fancybox.showLoading();"><i class="icon-money"></i><span>{l s='Payment Settings' mod='npsprzelewy24'}</span></a></li>
{if isset($p24_access_url) && !empty($p24_access_url)}
<li><a href="{$p24_access_url}" target="_blank"><i class="icon-rocket"></i><span>{l s='Go to Przelewy 24' mod='npsprzelewy24'}</span></a></li>
{/if}
