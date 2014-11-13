{if $video_url}
<div class="tab-pane fade" id="product_video_tab">
    <iframe type="text/html" width="640" height="390" src="{$video_url}" frameborder="0" allowFullScreen></iframe>
</div>
{/if}

<div class="tab-pane fade" id="product_map_tab">
    <div id="map-canvas" data-target="{$product_address}"></div>
</div>

{if $show_regulations}
<div class="tab-pane fade" id="seller_regulations_tab">
    <div class="rte">{$regulations[$current_id_lang]}</div>
</div>
{/if}