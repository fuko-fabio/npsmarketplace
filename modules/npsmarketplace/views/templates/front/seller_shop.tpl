{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Shop %s' mod='npsprzelewy24' sprintf=$seller['name']|escape:'html':'UTF-8' mod='npsmarketplace'}</span>
{/capture}
<div class="nps-seller-shop">
    <div class="primary_block row">
        <!-- left infos-->
        <div class="pb-left-column col-xs-6 col-sm-3 col-md-3">
            <!-- product img-->
            <div id="image-block" class="clearfix">
                {if $have_image}
                <span id="view_full_size"> <img id="bigpic" itemprop="image" src="{$seller['image']}?{time()}" /> </span>
                {else}
                <span id="view_full_size"> <img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" /> </span>
                {/if}
            </div>
            <!-- end image-block -->
        </div>
        <!-- end pb-left-column -->
        <!-- end left infos-->
        <!-- center infos -->
        <div class="pb-center-column col-xs-6 col-sm-9 col-md-9">
            <h1 itemprop="name">{$seller['name']|escape:'html':'UTF-8'}</h1>
            {if $seller['description'][$current_id_lang]}
            <p class="rte align_justify">
                <label>{l s='Description' mod='npsmarketplace'}</label><br />
                <div class="rte">{$seller['description'][$current_id_lang]}</div>
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
            <li><a href="#seller_company" data-toggle="tab">{l s='Company informations'  mod='npsmarketplace'}</a></li>
            <li><a href="#seller_regulations" data-toggle="tab">{l s='Regulations'  mod='npsmarketplace'}</a></li>
            {$HOOK_SELLER_TAB}
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="seller_events">
                {if $products}
                    <div class="content_sortPagiBar">
                        <div class="sortPagiBar clearfix">
                            {include file="$tpl_dir./product-sort.tpl"}
                            {include file="$tpl_dir./nbr-product-page.tpl"}
                        </div>
                        <div class="top-pagination-content clearfix">
                            {include file="$tpl_dir./pagination.tpl"}
                        </div>
                    </div>
                    {include file="$tpl_dir./product-list.tpl" products=$products}
                    <div class="content_sortPagiBar">
                        <div class="bottom-pagination-content clearfix">
                            {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
                        </div>
                    </div>
                {else}
                    <p class="alert alert-info"><span class="alert-content">{l s='No products in this shop.' mod='npsmarketplace'}</span></p>
                {/if}
            </div>

            <div class="tab-pane fade" id="seller_company">
                <table class="table table-bordered table-hover">
                    {if !empty($seller.company)}
                    <tr>
                        <td>{l s='Company Name' mod='npsmarketplace'}</td>
                        <td>{$seller.company}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.person)}
                    <tr>
                        <td>{l s='Person' mod='npsmarketplace'}</td>
                        <td>{$seller.person}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.address)}
                    <tr>
                        <td>{l s='Address' mod='npsmarketplace'}</td>
                        <td>{$seller.address}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.krs)}
                    <tr>
                        <td>{l s='KRS' mod='npsmarketplace'}</td>
                        <td>{$seller.krs}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.krs_reg)}
                    <tr>
                        <td>{l s='The KRS registration authority' mod='npsmarketplace'}</td>
                        <td>{$seller.krs_reg}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.nip)}
                    <tr>
                        <td>{l s='NIP' mod='npsmarketplace'}</td>
                        <td>{$seller.nip}</td>
                    </tr>
                    {/if}
                    {if !empty($seller.email)}
                    <tr>
                        <td>{l s='E-mail' mod='npsmarketplace'}</td>
                        <td><a href="mailto:{$seller.email}"><i class="icon-envelope"></i>  {$seller.email}</a>
                    </td>
                    </tr>
                    {/if}
                    {if !empty($seller.phone)}
                    <tr>
                        <td>{l s='Phone' mod='npsmarketplace'}</td>
                        <td><a href="tel:{$seller.phone}"><i class="icon-phone"></i>  {$seller.phone}</a></td>
                    </tr>
                    {/if}
                    {if !empty($seller.mobilephone)}
                    <tr>
                        <td>{l s='Mobilephone' mod='npsmarketplace'}</td>
                        <td><a href="tel:{$seller.mobilephone}"><i class="icon-phone"></i>  {$seller.mobilephone}</a></td>
                    </tr>
                    {/if}
                </table>
            </div>
            <div class="tab-pane fade" id="seller_regulations">
                {if empty($seller['regulations'][$current_id_lang])}
                    <p class="alert alert-warning"><span class="alert-content">{l s='Company Privacy Policy has not been completed.' mod='npsmarketplace'}</span></p>
                {else}
                    <div class="rte">{$seller['regulations'][$current_id_lang]}</div>
                {/if}
            </div>
            {$HOOK_SELLER_TAB_CONTENT}
        </div><!-- tab content -->
    </div>
</div>
