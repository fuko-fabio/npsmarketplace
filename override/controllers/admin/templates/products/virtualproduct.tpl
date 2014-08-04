{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var newLabel = '{l s='New label'}';
	var choose_language = '{l s='Choose language:'}';
	var required = '{l s='Required'}';
	var customizationUploadableFileNumber = '{$product->uploadable_files}';
	var customizationTextFieldNumber = '{$product->text_fields}';
	var uploadableFileLabel = 0;
	var textFieldLabel = 0;
</script>
<div id="product-virtualproduct" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="VirtualProduct" />
	<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
	<h3>{l s='Virtual Product (services, booking or downloadable products)'}</h3>
	<div class="is_virtual_good" class="form-group">
		<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
		<label for="is_virtual_good" class="t bold">{l s='Is this a virtual product?'}</label>
	</div>
	<div id="virtual_good" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} class="form-group">
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Does this product have an associated file?'}</label>
			<div class="col-lg-2">
				<span class="switch prestashop-switch">
					<input type="radio" name="is_virtual_file" id="is_virtual_file_on" value="1" {if $product_downloaded} checked="checked"{/if} />
					<label for="is_virtual_file_on">{l s='Yes'}</label>
					<input type="radio" name="is_virtual_file" id="is_virtual_file_off" value="0" {if !$product_downloaded} checked="checked"{/if} />
					<label for="is_virtual_file_off">{l s='No'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div id="is_virtual_file_product" style="display:none;">
			{if $download_product_file_missing}
			<div class="form-group">
				<div class="col-lg-push-3 col-lg-9">
					<div class="alert alert-danger" id="file_missing">
						{$download_product_file_missing} :<br/>
						<strong>{l s='Server file name : %s'|sprintf:$product->productDownload->filename}</strong>
					</div>
				</div>
			</div>
			{/if}
			{if !$download_dir_writable}
			<div class="form-group">
				<div class="col-lg-push-3 col-lg-9">
					<div class="alert alert-danger">
						{l s='Your download repository is not writable.'}
					</div>
				</div>
			</div>
			{/if}
			{if $product->productDownload->id}
                <input type="hidden" id="virtual_product_id" name="virtual_product_id" value="{$product->productDownload->id}" />
            {/if}
            <table cellpadding="5" style="float: left; margin-left: 10px;">
                <tr id="upload_input" {if $is_file}style="display:none"{/if}>
                    <td class="col-left">
                        <label id="virtual_product_file_label" for="virtual_product_file" class="t">{l s='Upload a file'}</label>
                    </td>
                    <td class="col-right">
                        <input type="file" id="virtual_product_file" name="virtual_product_file" onchange="uploadFile();" maxlength="{$upload_max_filesize}" />
                        <p class="preference_description">{l s='Your server\'s maximum file-upload size is'}:&nbsp;{$upload_max_filesize} {l s='MB'}</p>
                    </td>
                </tr>
                <tr id="upload-error" style="display:none">
                    <td colspan=2></td>
                </tr>
                <tr id="upload-confirmation" style="display:none">
                    <td colspan=2>
                        {if $up_filename}
                            <input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$up_filename}" />
                        {/if}
                        <div class="conf">
                        <script>
                            delete_this_file = '{l s='Delete this file'}';
                        </script>
                            <a class="delete_virtual_product" id="delete_downloadable_product" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">
                                <img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
                            </a>
                        </div>
                    </td>
                </tr>
                {if $is_file}
                    <tr>
                        <td class="col-left">
                            <input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
                            <label class="t">{l s='Link to the file:'}</label>
                        </td>
                         <td class="col-right">
                            {$product->productDownload->getHtmlLink(false, true)}
                            <a href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red delete_virtual_product">
                                <img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
                            </a>
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td class="col-left">
                        <label for="virtual_product_name" class="t">{l s='Filename'}</label>
                    </td>
                    <td class="col-right">
                        <input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="{$product->productDownload->display_filename|escape:'htmlall':'UTF-8'}" />
                        <p class="preference_description" name="help_box">{l s='The full filename with its extension (e.g. Book.pdf)'}</p>
                    </td>
                </tr>
                <tr>
                    <td class="col-left">
                        <label for="virtual_product_nb_downloable" class="t">{l s='Number of allowed downloads'}</label>
                    </td>
                    <td class="col-right">
                        <input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$product->productDownload->nb_downloadable|htmlentities}" class="" size="6" />
                        <p class="preference_description">{l s='Number of downloads allowed per customer. (Set to 0 for unlimited downloads)'}</p>
                    </td>
                </tr>
                <tr>
                    <td class="col-left">
                        <label for="virtual_product_expiration_date" class="t">{l s='Expiration date'}</label>
                    </td>
                    <td class="col-right">
                        <input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$product->productDownload->date_expiration}" size="11" maxlength="10" autocomplete="off" /> {l s='Format: YYYY-MM-DD'}
                        <p class="preference_description">{l s='If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.'}</p>
                    </td>
                </tr>
                    <td class="col-left">
                        <label for="virtual_product_nb_days" class="t">{l s='Number of days'}</label>
                    </td>
                    <td class="col-right">
                        <input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="{$product->productDownload->nb_days_accessible|htmlentities}" class="" size="4" /><sup> *</sup>
                        <p class="preference_description">{l s='Number of days this file can be accessed by customers'} - <em>({l s='Set to zero for unlimited access.'})</em></p>
                    </td>
                </tr>
                {* Feature not implemented *}
                {*<tr>*}
                    {*<td class="col-left">*}
                        {*<label for="virtual_product_is_shareable" class="t">{l s='is shareable'}</label>*}
                    {*</td>*}
                    {*<td class="col-right">*}
                        {*<input type="checkbox" id="virtual_product_is_shareable" name="virtual_product_is_shareable" value="1" {if $product->productDownload->is_shareable}checked="checked"{/if} />*}
                        {*<span class="hint" name="help_box" style="display:none">{l s='Please specify if the file can be shared.'}</span>*}
                    {*</td>*}
                {*</tr>*}
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
	</div>
</div>