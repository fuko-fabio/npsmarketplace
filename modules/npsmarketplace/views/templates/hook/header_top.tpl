{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

{if count($towns) > 1}
<div id="nps-towns-top">
    <div id="towns">
        <ul id="first-languages" class="countries_ul">
        {foreach from=$towns key=k item=town name="towns"}
            <li {if $cookie->main_town == $town.id_town}class="selected_town"{/if}>
                <a href="#" onclick="changeMainTown({$town.id_town});">{$town.name}</a>
            </li>
        {/foreach}
        </ul>
    </div>
</div>
{/if}


<div class="nps-header-sell">
    <a href="{$link->getModuleLink('npsmarketplace', 'Product')}"><i class="icon-dollar"></i> {l s='Sell' mod='npsmarketplace'}</a>
</div>
