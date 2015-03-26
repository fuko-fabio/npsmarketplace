{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}

{addJsDefL name=dictDefaultMessage}{l s='Drop files here to upload' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictFallbackMessage}{l s='Your browser does not support drag\'n\'drop file uploads.' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictFallbackText}{l s='Please use the fallback form below to upload your files like in the olden days.' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictFileTooBig}{l s='File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictInvalidFileType}{l s='You can\'t upload files of this type.' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictResponseError}{l s='Server responded with {{statusCode}} code.' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictCancelUpload}{l s='Cancel upload' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictCancelUploadConfirmation}{l s='Are you sure you want to cancel this upload?' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictRemoveFile}{l s='Remove' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictMaxFilesExceeded}{l s='You can not upload more than %s files.' mod='npsmarketplace' js=1 sprintf=$max_images}{/addJsDefL}
{addJsDefL name=maxImages}{$max_images}{/addJsDefL}
{addJsDefL name=maxImageSize}{$max_image_size}{/addJsDefL}
{addJsDefL name=dropzoneServerUrl}{$dropzone_url}{/addJsDefL}
{addJsDefL name=dictTownsOther}{l s='--Other--' mod='npsmarketplace' js=1}{/addJsDefL}

<script>
    var dropzoneImages = {$product['images']|json_encode};
    var provincesMap = {json_encode($provinces)};
</script>

{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
{if isset($product['id'])}
<span class="navigation_page">{l s='Edit Event' mod='npsmarketplace'}</span>
{else}
<span class="navigation_page">{l s='Add Event' mod='npsmarketplace'}</span>
{/if}
{/capture}
{if isset($product['id'])}
<h1 class="page-heading bottom-indent">{l s='Edit Event' mod='npsmarketplace'}</h1>
{else}
<h1 class="page-heading bottom-indent">{l s='Add Event' mod='npsmarketplace'}</h1>
{/if}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-product">
    <form role="form" action="{$request_uri}" method="post" id="edit-product-form">
        <input type="hidden" name="form_token" value="{$form_token}" />
        {include file="$product_fieldset_tpl_path" categories_tree=$categories_tree category_partial_tpl_path=$category_partial_tpl_path}
        {if !isset($product['id'])}
            {l s='By clicking "Save" I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsmarketplace'}</a>
        {/if}
        <button type="submit" class="btn btn-default button button-medium pull-right" name="saveProduct"><span>{l s='Save' mod='npsmarketplace'} <i class="icon-save right"></i></span></button>
    </form>
    
    <!-- Fancybox -->
<div style="display:none">
    <div id="event_combination" class="event-combination">
        <h2 class="page-subheading">{l s='New variant' mod='npsmarketplace'}</h2>
        <form id="event_combination_form">
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="required">{l s='Type' mod='npsmarketplace'}</label>
                    <select class="form-control" name="type">
                        <option value="0" selected="">{l s='Ticket' mod='npsmarketplace'}</option> 
                        <option value="1">{l s='Carnet' mod='npsmarketplace'}</option> 
                        <option value="2">{l s='Advertisement' mod='npsmarketplace'}</option> 
                        {if $seller->outer_adds}
                        <option value="3">{l s='External Advertisement' mod='npsmarketplace'}</option> 
                        {/if}
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="required">{l s='Name' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name"/>
                </div>
            </div>
            <div class="row ticket-row">
                <div id="date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Event date' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="date" data-format="yyyy-MM-dd" type="text"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Event hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row">
                <div id="expiry_date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="expiry_time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="expiry_time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row price-row">
                <div class="form-group col-md-6">
                    <label class="required">{l s='Price' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isPrice" type="text" name="price" required="" />
                    <span class="form_info">{l s='Final price visible for customers. Example: 120.50' mod='npsmarketplace'}</span>
                </div>
                <div class="form-group col-md-6">
                    <label class="required">{l s='Quantity' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isQuantity" type="number" name="quantity" required="" />
                    <span class="form_info">{l s='Warning: Check quantity before submit' mod='npsmarketplace'}</span>
                </div>
            </div>
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
            <input class="button" onclick="addVariant();" value="{l s='OK' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>
<!-- End fancybox -->

</div>
