{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<div class="nps-product-seller-info">
    <a href="{$seller_shop_url}"><span class="name">{$seller->name}</span><span class="logo">{if $logo}<a href="{$seller_shop_url}"><img src="{$logo}" /></a>{/if}</span></a>
    <div class="content">
        {if $p1 || $p2}
        <div class="row">
            {if $p1}
            <div class="col-xs-6 col-sm-6 col-md-6">
                <a href="{$p1.url}"><img src="{$p1.img}" /></a>
            </div>
            {/if}
            {if $p2}
            <div class="col-xs-6 col-sm-6 col-md-6">
                <a href="{$p2.url}"><img src="{$p2.img}" /></a>
            </div>
            {else}
            <div class="col-xs-6 col-sm-6 col-md-6">
                <a href="{$seller_shop_url}"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" /><span>{l s='See more' mod='npsmarketplace'}</span></a>
            </div>
            {/if}
        </div>
        {/if}
        {if $p1 && $p2}
        <div class="row">
            {if $p3}
            <div class="col-xs-6 col-sm-6 col-md-6">
                <a href="{$p3.url}"><img src="{$p3.img}" /></a>
            </div>
            {/if}
            <div class="col-xs-6 col-sm-6 col-md-6">
                <a href="{$seller_shop_url}"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" /><span>{l s='See more' mod='npsmarketplace'}</span></a>
            </div>
        </div>
        {/if}
    </div>
</div>