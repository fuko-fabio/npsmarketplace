{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{if $video_url}
<div class="tab-pane fade" id="product_video_tab">
    <iframe width="100%" height="490" src="{$video_url}" frameborder="0" allowfullscreen></iframe>
</div>
{/if}

<div class="tab-pane fade" id="product_map_tab">
    <table class="table-data-sheet">
        {if !empty($product_address)}
        <tr>
            <td>{l s='Address' mod='npsmarketplace'}</td>
            <td>{$product_address}</td>
        </tr>
        {/if}
        {if !empty($product_district)}
        <tr>
            <td>{l s='District' mod='npsmarketplace'}</td>
            <td>{$product_district}</td>
        </tr>
        {/if}
        {if !empty($product_town)}
        <tr>
            <td>{l s='Town' mod='npsmarketplace'}</td>
            <td>{$product_town}</td>
        </tr>
        {/if}
    </table>
    <div id="map-canvas" data-target="{$product_address}"></div>
</div>

<div class="tab-pane fade" id="seller_regulations_tab">
    <div class="rte">{$regulations[$current_id_lang]}</div>
</div>
