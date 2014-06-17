{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Become a seller' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Become a seller' mod='npsmarketplace'}</h1>
{if $account_state == 0}
<p class="info-title">{l s='Send a request for permission to sell tickets on our website.' mod='npsmarketplace'}</p>
{else if $account_state == 1}
<p class="info-title">{l s='Your request has been sent to us on %s. Please wait for contact with our marketing team.' sprintf=$account_request_date mod='npsmarketplace'}</p>
{/if}
{if $account_state == 0}
<form action="{$request_uri|escape:'html':'UTF-8'}" method="post" class="std" id="formaccountrequest">
    <fieldset>
        <div class="form-group">
            <label for="company_logo">{l s='Company Logo' mod='npsmarketplace'}</label>
            <button type="button">Upload</button>
            <span class="form_info">{l s='Required size 200 x 200' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group">
            <label for="company_name">{l s='Company Name' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name" required="" value="{if isset($smarty.post.company_name)}{$smarty.post.company_name|escape:'html':'UTF-8'|stripslashes}{/if}"/>
        </div>
        <div class="form-group">
            <label for="company_description">{l s='Company Description' mod='npsmarketplace'}</label>
            <textarea class="validate form-control" data-validate="isMessage" id="company_description" name="company_description" rows="6" value="{if isset($smarty.post.company_description)}{$smarty.post.company_description|escape:'html':'UTF-8'|stripslashes}{/if}">
            </textarea>
        </div>
        <div class="form-group">
            <label for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name" required="" value="{if isset($smarty.post.seller_name)}{$smarty.post.seller_name|escape:'html':'UTF-8'|stripslashes}{/if}"/>
        </div>
        <div class="form-group">
            <label for="seller_phone">{l s='Phone Number' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isPhoneNumber" type="tel" id="seller_phone" name="seller_phone" required="" value="{if isset($smarty.post.seller_phone)}{$smarty.post.seller_phone|escape:'html':'UTF-8'|stripslashes}{/if}"/>
        </div>
        <div class="form-group">
            <label for="seller_email">{l s='Buisness Email' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isEmail" type="text" id="seller_email" name="seller_email" required="" value="{if isset($smarty.post.seller_email)}{$smarty.post.seller_email|escape:'html':'UTF-8'|stripslashes}{/if}"/>
        </div>
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Send request' mod='npsmarketplace'}<i class="icon-share right"></i></span></button>
        </p>
    </fieldset>
</form>
{/if}
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