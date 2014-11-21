{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

{if count($towns) > 1}
    <div class="nps-towns-top">
        {foreach from=$towns key=k item=town name="towns"}
             {if $cookie->main_town == $town.id_town}
                <div class="current">
                    <span>{$town.name}</span>
                </div>
            {/if}
        {/foreach}
        <ul class="towns-block_ul toogle_content">
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
