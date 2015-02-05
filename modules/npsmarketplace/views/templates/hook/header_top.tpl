{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}

<div class="nps-location-top">
    {if $cookie->main_province == 0}
        <div class="current">
            <span>{l s='All' mod='npsmarketplace'}</span>
        </div>
    {else}
        {foreach from=$provinces key=k item=province name="provinces"}
            {if $cookie->main_province == $province.id_province}
                <div class="current">
                    <span>{$province.name}</span>
                </div>
            {/if}
        {/foreach}
    {/if}
    <div class="location-selector">
        <ul class="parent-menu">
            <li {if $cookie->main_province == 0}class="selected"{/if}>
                <a href="#" onclick="changeMainProvince(0);">{l s='All' mod='npsmarketplace'}</a>
            </li>
            {foreach from=$provinces key=k item=province name="provinces"}
                <li class="province {if $cookie->main_province == $province.id_province}selected{/if}">
                    {if count($province.towns) > 1}
                    <ul class="towns-block_ul toogle_content">
                        {foreach from=$province.towns key=k item=town name="towns"}
                            <li {if $cookie->main_town == $town.id_town}class="selected"{/if}>
                                <a href="#" onclick="changeMainTown({$town.id_town});">{$town.name}</a>
                            </li>
                        {/foreach}
                    </ul>
                    {/if}
                    <a href="#" onclick="changeMainProvince({$province.id_province});">{$province.name}</a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>


<div class="nps-header-sell">
    <a href="{$link->getModuleLink('npsmarketplace', 'Product')}"><i class="icon-dollar"></i> {l s='Sell' mod='npsmarketplace'}</a>
</div>