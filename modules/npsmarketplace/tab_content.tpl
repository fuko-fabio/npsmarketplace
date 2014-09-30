<div class="tab-pane fade" id="product_map_tab">
    <div id="map-canvas" data-target="{$product_address}"></div>
</div>

{if $show_regulations}
<div class="tab-pane fade" id="seller_regulations_tab">
    <div class="rte">{$regulations[$current_id_lang]}</div>
</div>
{/if}