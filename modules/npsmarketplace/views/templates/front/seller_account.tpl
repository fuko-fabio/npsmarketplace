{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Shop Profile'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="box">
    {include file="$tpl_dir./errors.tpl"}
    <h1 class="page-heading bottom-indent">{l s='My Shop Profile' mod='npsmarketplace'}</h1>
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="selleraccount">
        {include file="$seller_fieldset_tpl_path"}
        <p class="submit">
            <button type="submit" class="btn btn-default button button-medium"><span>{l s='Save' mod='npsmarketplace'}<i class="icon-save right"></i></span></button>
        </p>
    </form>
</div>

<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir}">
			<span><i class="icon-chevron-left"></i> {l s='Home'}</span>
		</a>
	</li>
</ul>