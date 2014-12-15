{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}
{if count($towns) > 1}
    <div class="nps-towns-top">
        {if $cookie->main_town == 0}
            <div class="current">
                <span>{l s='All' mod='npsmarketplace'}</span>
            </div>
        {else}
            {foreach from=$towns key=k item=town name="towns"}
                 {if $cookie->main_town == $town.id_town}
                    <div class="current">
                        <span>{$town.name}</span>
                    </div>
                {/if}
            {/foreach}
        {/if}
        <ul class="towns-block_ul toogle_content">
            <li {if $cookie->main_town == 0}class="selected"{/if}>
                <a href="#" onclick="changeMainTown(0);">{l s='All' mod='npsmarketplace'}</a>
            </li>
            {foreach from=$towns key=k item=town name="towns"}
                <li {if $cookie->main_town == $town.id_town}class="selected"{/if}>
                    <a href="#" onclick="changeMainTown({$town.id_town});">{$town.name}</a>
                </li>
            {/foreach}
        </ul>
    </div>
 {/if}
<div class="nps-header-sell">
    <a href="{$link->getModuleLink('npsmarketplace', 'Product')}"><i class="icon-dollar"></i> {l s='Sell' mod='npsmarketplace'}</a>
</div>