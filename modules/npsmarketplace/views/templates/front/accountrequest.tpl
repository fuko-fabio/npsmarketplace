{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Become a seller' mod='npsmarketplace'}</span>
{/capture}
<div class="box">
    {include file="$tpl_dir./errors.tpl"}
    <h1 class="page-heading bottom-indent">{l s='Become a seller' mod='npsmarketplace'}</h1>
    {if $account_state == 0}
    <p class="info-title">{l s='Send a request for permission to sell tickets on our website.' mod='npsmarketplace'}</p>
    {else if $account_state == 1}
    <p class="info-title">{l s='Your request has been sent to us on %s. Please wait for contact with our marketing team.' sprintf=$account_request_date mod='npsmarketplace'}</p>
    {/if}
    {if $account_state == 0}
    <form action="{$request_uri|escape:'html':'UTF-8'}" method="post" class="std" id="formaccountrequest">
        <h3 class="page-heading bottom-indent">{l s='Seller profile' mod='npsmarketplace'}</h3>
        <fieldset>
            <div class="form-group">
                <label for="company_logo">{l s='Company Logo' mod='npsmarketplace'}</label></br>
                <img src="" width="200px" height="200px" />
                <button type="button">Upload</button></br>
                <span class="form_info">{l s='Required size 200 x 200' mod='npsmarketplace'}</span>
            </div>
            <div class="form-group">
                <label class="required" for="company_name">{l s='Company Name' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="company_description">{l s='Company Description' mod='npsmarketplace'}</label>
                <textarea class="validate form-control" data-validate="isMessage" id="company_description" name="company_description" rows="6"></textarea>
            </div>
            <div class="form-group">
                <label class="required" for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="seller_phone">{l s='Phone Number' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isPhoneNumber" type="tel" id="seller_phone" name="seller_phone" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="seller_email">{l s='Buisness Email' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isEmail" type="text" id="seller_email" name="seller_email" required=""/>
            </div>
        </fieldset>
        <h3 class="page-heading bottom-indent">{l s='First offer' mod='npsmarketplace'}</h3>
        <fieldset>
            <div id="zjquery" class="form-group dropzone">
              <label>{l s='Drop pictures inside...' mod='npsmarketplace'}</label>
              <p>{l s='Or click here to Browse' mod='npsmarketplace'}</p>
            </div>
            <div class="form-group">
                <label class="required" for="product_name">{l s='Name' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_name" name="product_name" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="product_short_description">{l s='Short Description' mod='npsmarketplace'}</label>
                <textarea class="validate form-control" data-validate="isMessage" id="product_short_description" name="product_short_description" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label class="required" for="product_description">{l s='Description' mod='npsmarketplace'}</label>
                <textarea class="validate form-control" data-validate="isMessage" id="product_description" name="product_description" rows="10"></textarea>
            </div>
            <div class="form-group">
                <label class="required" for="product_amount">{l s='Amount' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isNumber" type="number" id="product_amount" name="product_amount" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isUnsignedFloat" type="text" id="product_price" name="product_price" required=""/>
            </div>
            <div class="form-group">
                <label class="required" for="product_category">{l s='Category' mod='npsmarketplace'}</label>
                <ul class="tree">
                {foreach from=$categories_tree.children item=child name=categories_tree}
                    {if $smarty.foreach.categories_tree.last}
                        {include file="$category_partial_tpl_path" node=$child last='true'}
                    {else}
                        {include file="$category_partial_tpl_path" node=$child}
                    {/if}
                {/foreach}
                </ul>
            </div>
            <div class="form-group">
                <label class="required" for="product_code">{l s='Code' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_code" name="product_code" required=""/>
            </div>
        </fieldset>
        <p class="required"><sup>*</sup>{l s='Required field' mod='npsmarketplace'}</p>
    
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Send request' mod='npsmarketplace'}<i class="icon-share right"></i></span></button>
        </p>
    </form>
    {/if}
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='npsmarketplace'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir}">
			<span><i class="icon-chevron-left"></i> {l s='Home' mod='npsmarketplace'}</span>
		</a>
	</li>
</ul>