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

{if isset($smarty.post.combinations)}
{assign var="combinations" value=$smarty.post.combinations}
{else}
{assign var="combinations" value=$product.combinations}
{/if}
<script>
    var dropzoneImages = {$product['images']|json_encode};
    var provincesMap = {json_encode($provinces)};
    var productCombinations = {json_encode(array_values($combinations))};
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
    {include file="$variants_tpl_path"}
</div>
