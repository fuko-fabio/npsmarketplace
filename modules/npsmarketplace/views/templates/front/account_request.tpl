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
    {if $account_state == 'requested'}
    <p class="info-title">{l s='Your request has been sent to us on %s. Please wait for contact with our marketing team.' sprintf=$account_request_date mod='npsmarketplace'}</p>
    {else if $account_state == 'locked'}
    <p class="info-title">{l s='Your account has been locked by administrator'}</p>
    {/if}
    {if $account_state == 'none'}
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="formaccountrequest">
        {include file="$seller_fieldset_tpl_path"}
        <label class="required">{l s='Required field' mod='npsmarketplace'}</label>
        </br>
        <strong>{l s='By clicking "Submit" I agree that:' mod='npsmarketplace'}</strong>
        <ul>
            <li>{l s='I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.'}</a></li>
            <li>{l s='I give consent to the' mod='npsmarketplace'} <a href="{$processing_data_url}">{l s='processing of my data.'} </a></li>
        </ul>
        </br>
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Submit' mod='npsmarketplace'}<i class="icon-share right"></i></span></button>
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