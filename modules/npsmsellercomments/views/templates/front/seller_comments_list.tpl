{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Comments'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Comments'}</h1>
<div class="block-center" id="block-seller-account">
    <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#received_comments" role="tab" data-toggle="tab">{l s='Received Comments' mod=npsmsellercomments}</a></li>
      <li><a href="#post_comments" role="tab" data-toggle="tab">{l s='Post Comments' mod=npsmsellercomments}</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="received_comments">

        </div>
        <div class="tab-pane" id="received_comments">

        </div>
    </div>
</div>