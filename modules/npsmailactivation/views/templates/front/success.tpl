<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
{capture name=path}{l s='Account activation' mod='npsmailactivation'}{/capture}

<h1 class="page-heading bottom-indent">{l s='Account activation' mod='npsmailactivation'}</h1>

<div class="block-center" id="block-account-activation">
    <div class="alert alert-success">
    {l s='Your account was activated successfully.' mod='npsmailactivation'}
    </div>
    <h3><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='Sign in' mod='npsmailactivation'} </a></h3>
</div>