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
{addJsDefL name=dictNow}{l s='Now' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictDone}{l s='Done' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictChooseTime}{l s='Choose Time' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictTime}{l s='Time' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictHour}{l s='Hour' mod='npsmarketplace' js=1}{/addJsDefL}
{addJsDefL name=dictMinute}{l s='Minute' mod='npsmarketplace' js=1}{/addJsDefL}

{if isset($smarty.post.combinations)}
{assign var="combinations" value=$smarty.post.combinations}
{else}
{assign var="combinations" value=$product.combinations}
{/if}

{if isset($smarty.post.questions)}
{assign var="questions" value=$smarty.post.questions}
{else}
{assign var="questions" value=$product.questions}
{/if}
<script>
    var dropzoneImages = {$product['images']|json_encode};
    var provincesMap = {json_encode($provinces)};
    var productCombinations = {json_encode(array_values($combinations))};
    var productQuestions = {json_encode(array_values($questions))};
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
<h1 class="page-heading bottom-indent with-button">{l s='Add Event' mod='npsmarketplace'}<button class="get-tour-button pull-right" type="button" onclick="startNewEventTour();"><i class="icon-info"></i></button></h1>
{/if}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-product">
    <form role="form" action="{$request_uri}" method="post" id="edit-product-form" onsubmit="return validateForm()">
        <p class="alert alert-error validation-error" style="display: none"><span class="alert-content">{l s='Form contains errors, please check form and fix issues.' mod='npsmarketplace'}</span></p>
        <input type="hidden" name="form_token" value="{$form_token}" />
        {include file="$product_fieldset_tpl_path" categories_tree=$categories_tree category_partial_tpl_path=$category_partial_tpl_path}
        {if !isset($product['id'])}
            {l s='By clicking "Save" I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsmarketplace'}</a>
        {/if}
        <button type="submit" class="btn btn-default button button-medium pull-right" name="saveProduct"><span>{l s='Save' mod='npsmarketplace'} <i class="icon-save right"></i></span></button>
    </form>
    {include file="$variants_tpl_path"}
    {include file="$questions_tpl_path"}
</div>
