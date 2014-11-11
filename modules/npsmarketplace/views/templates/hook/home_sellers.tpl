{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<div class="nps-sellers-list">
    <h2>{l s='Our shops' mod='npsmarketplace'}</h2>
    {if $home_sellers|@count > 0}
    <div class="row">
        {foreach from=$home_sellers item=seller}
            <div class="item col-xs-6 col-sm-4 col-md-3">
                <a href="{$seller.url}">
                    {if $seller.img != null}
                    <img src="{$seller.img}"/>
                    {else}
                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" />
                    <span class="name">{$seller.name}</span>
                    {/if}
                    <span class="mask">{$seller.name}</span>
                </a>
            </div>
        {/foreach}
    </div>
    {else}
    <p class="alert alert-info">{l s='No sellers available' mod='npsmarketplace'}</p>
</div>