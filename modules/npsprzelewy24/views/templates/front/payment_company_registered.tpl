{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Payment Settings' mod='npsprzelewy24'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Payment Settings' mod='npsprzelewy24'}</h1>
<div class="block-center" id="block-seller-payment-settings">
    {include file="$tpl_dir./errors.tpl"}

    {if !empty($register_link)}
        <div class="alert alert-success">
            <span class="alert-content">
            {l s='Registration require confirmation' mod='npsprzelewy24'} <a class="alert-link" target="_blank" href="{$register_link}">{l s='Click here' mod='npsprzelewy24'}</a> {l s='to finalize registration' mod='npsprzelewy24'}
        </span>
        </div>
    {else}
        {if !isset($errors) || empty($errors)}
        <div class="alert alert-info">
            <p class="alert-content">{l s='Company has been registered in Przelewy24 service on %s' sprintf=$company->registration_date mod='npsprzelewy24'}</p>
        </div>
        {/if}
    {/if}

    <form>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="company_name">{l s='Company Name' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="company_name" name="company_name" readonly=""
                value="{if isset($company->company_name)}{$company->company_name|escape:'html':'UTF-8'}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="company_name">{l s='Przelewy24 Seller ID' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="spid" name="spid" readonly=""
                value="{if isset($company->spid)}{$company->spid|escape:'html':'UTF-8'}{/if}"/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="person">{l s='Person' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="person" name="person" readonly=""
                value="{if isset($company->person)}{$company->person|escape:'html':'UTF-8'}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="email">{l s='Email' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="email" name="email" readonly=""
                value="{if isset($company->email)}{$company->email|escape:'html':'UTF-8'}{/if}"/>
            </div>
        </div>
        <div class="form-group">
            <label for="street">{l s='Address' mod='npsprzelewy24'}</label>
            <input class="form-control" type="text" id="street" name="street" readonly=""
            value="{if isset($company->street)}{$company->street|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="post_code">{l s='Post code' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="post_code" name="post_code" readonly=""
                value="{if isset($company->post_code)}{$company->post_code|escape:'html':'UTF-8'}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="city">{l s='City' mod='npsprzelewy24'}</label>
                <input class="form-control" type="text" id="city" name="city" readonly=""
                value="{if isset($company->city)}{$company->city|escape:'html':'UTF-8'}{/if}"/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="nip">{l s='NIP' mod='npsprzelewy24'}</label>
                <input class="form-control" type="number" id="nip" name="nip" readonly=""
                value="{if isset($company->nip)}{$company->nip|escape:'html':'UTF-8'}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="regon">{l s='Regon' mod='npsprzelewy24'}</label>
                <input class="form-control" type="number" id="regon" name="regon" readonly=""
                value="{if isset($company->regon)}{$company->regon|escape:'html':'UTF-8'}{/if}"/>
            </div>
        </div>
        <div class="form-group">
            <label for="iban">{l s='Bank account number' mod='npsprzelewy24'}</label>
            <input class="form-control" type="text" id="iban" name="iban" readonly=""
            value="{if isset($company->iban)}{$company->iban|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="required form-group">
            <label>
                <a href="{$p24_agreement_url}">{l s='“Regulations of Przelewy24”' mod='npsprzelewy24'}</a> {l s='Acceptted on %s' mod='npsprzelewy24' sprintf=$company->registration_date} 
            </label>
        </div>
    </form>
</div>