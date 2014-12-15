{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Unlock Account' mod='npsmarketplace'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Unlock Account' mod='npsmarketplace'}</h1>
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-account">
    <form action="{$request_uri}" method="post">
         {if $sent}
         <p class="alert alert-info"><span class="alert-content">{l s='Your message has been sent to us. Please wait for answer.' mod='npsmarketplace'}</span></p>
         {else}
         <p class="alert alert-info"><span class="alert-content">{l s='Your accoun has been locked. You should recevie an email from our service with lock cause. Here you can send message directly to us.' mod='npsmarketplace'}</span></p>
         {/if}
         <div class="row">
            <div class="form-group col-md-12">
                <label class="required" for="seller_phone">{l s='Title' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isMessage" name="title" required="" value="{if isset($smarty.post.title)}{$smarty.post.title}{/if}"/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label class="required">{l s='Message' mod='npsmarketplace'}</label>
                <textarea class="is_required validate form-control textarea-autosize" data-validate="isMessage" name="message">{if isset($smarty.post.message)}{$smarty.post.message}{/if}</textarea>
            </div>
        </div>
        <script type="text/javascript">
            $(".textarea-autosize").autosize();
        </script>
        <button type="submit" class="btn btn-default button button-medium pull-right" name="submitMessage"><span>{l s='Send' mod='npsmarketplace'} <i class="icon-share right"></i></span></button>
    </form>
</div>