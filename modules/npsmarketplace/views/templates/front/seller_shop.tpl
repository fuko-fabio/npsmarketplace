{capture name=path}
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Shop %s' mod='npsprzelewy24' sprintf=$seller['name']|escape:'html':'UTF-8'}</span>
{/capture}
<div class="primary_block row">
    <!-- left infos-->
    <div class="pb-left-column col-xs-12 col-sm-4 col-md-5">
        <!-- product img-->
        <div id="image-block" class="clearfix">
            {if $have_image}
            <span id="view_full_size"> <img id="bigpic" itemprop="image" src="{$seller['image']}?{time()}" width="{$largeSize.width}" height="{$largeSize.height}"/> </span>
            {else}
            <span id="view_full_size"> <img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}"/> </span>
            {/if}
        </div>
        <!-- end image-block -->
    </div>
    <!-- end pb-left-column -->
    <!-- end left infos-->
    <!-- center infos -->
    <div class="pb-center-column col-xs-12 col-sm-4">
        <h1 itemprop="name">{$seller['name']|escape:'html':'UTF-8'}</h1>
        <br />
        <p>
            <label>{l s='Company name' mod='npsmarketplace'}</label><br />
            {$seller['company_name']}
        </p>
        {if $seller['company_description'][$current_id_lang]}
        <p class="rte align_justify">
            <label>{l s='Company description' mod='npsmarketplace'}</label><br />
            {$seller['company_description'][$current_id_lang]}
        </p>
        <!-- end short_description_block -->
        {/if}
    </div>
    <!-- end center infos-->
</div>
<!-- end primary_block -->
<br />
<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#seller_events" data-toggle="tab">{l s='Events'  mod='npsmarketplace'}</a></li>
        <li><a href="#seller_comments" data-toggle="tab">{l s='Comments'  mod='npsmarketplace'}</a></li>
        {if $seller['regulations_active']}
        <li><a href="#seller_regulations" data-toggle="tab">{l s='Regulations'  mod='npsmarketplace'}</a></li>
        {/if}
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active" id="seller_events">

        </div>
        <div class="tab-pane fade" id="seller_comments">

        </div>
        {if $seller['regulations_active']}
        <div class="tab-pane fade" id="seller_regulations">
            <textarea class="form-control" readonly="">{$seller['regulations'][$current_id_lang]}</textarea>
        </div>
        {/if}
    </div><!-- tab content -->
</div>

