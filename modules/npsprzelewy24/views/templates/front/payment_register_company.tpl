{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Configure Payment' mod='npsprzelewy24'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Payment Settings' mod='npsprzelewy24'}</h1>
<div class="block-center" id="block-seller-payment-settings">
    {include file="$tpl_dir./errors.tpl"}
    {if !isset($errors) || empty($errors)}
        {if isset($add_product) && $add_product}
            <div class="alert alert-info">
                <p class="alert-content">{l s='To be able add next event you need to configure your payment accont.' mod='npsprzelewy24'}</p>
            </div>
        {/if}
        <div class="alert alert-info">
            <p class="alert-content">{l s='You will be not able to edit this informations in future. Please fill form carefully.' mod='npsprzelewy24'}</p>
        </div>
    {/if}
    <form role="form" action="{$request_uri}" method="post">
        <div class="form-group">
            <label class="required" for="company_name">{l s='Company Name' mod='npsprzelewy24'}</label>
            <input class="validate is_required form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name" required=""
            value="{if isset($smarty.post.company_name)}{$smarty.post.company_name}{else}{if isset($company['company_name'])}{$company['company_name']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="person">{l s='Person' mod='npsprzelewy24'}</label>
                <input class="validate is_required form-control" data-validate="isName" type="text" id="person" name="person" required=""
                value="{if isset($smarty.post.person)}{$smarty.post.person}{else}{if isset($company['person'])}{$company['person']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="email">{l s='Email' mod='npsprzelewy24'}</label>
                <input class="validate is_required form-control" data-validate="isEmail" type="text" id="email" name="email" required=""
                value="{if isset($smarty.post.email)}{$smarty.post.email}{else}{if isset($company['email'])}{$company['email']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        <div class="form-group">
            <label class="required" for="street">{l s='Address' mod='npsprzelewy24'}</label>
            <input class="validate is_required form-control" data-validate="isAddress" type="text" id="street" name="street" required=""
            value="{if isset($smarty.post.street)}{$smarty.post.street}{else}{if isset($company['street'])}{$company['street']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="post_code">{l s='Post code' mod='npsprzelewy24'}</label>
                <input class="validate is_required form-control" data-validate="isPostCode" type="text" id="post_code" name="post_code" required=""
                value="{if isset($smarty.post.post_code)}{$smarty.post.post_code}{else}{if isset($company['post_code'])}{$company['post_code']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="city">{l s='City' mod='npsprzelewy24'}</label>
                <input class="validate is_required form-control" data-validate="isCityName" type="text" id="city" name="city" required=""
                value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($company['city'])}{$company['city']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="nip">{l s='NIP' mod='npsprzelewy24'}</label>
                <input class="validate is_required form-control" data-validate="isNip" type="number" id="nip" name="nip" required=""
                value="{if isset($smarty.post.nip)}{$smarty.post.nip}{else}{if isset($company['nip'])}{$company['nip']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="regon">{l s='Regon' mod='npsprzelewy24'}</label>
                <input class="validate form-control" data-validate="isRegon" type="number" id="regon" name="regon"
                value="{if isset($smarty.post.regon)}{$smarty.post.regon}{else}{if isset($company['regon'])}{$company['regon']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        <div class="form-group">
            <label class="required" for="iban">{l s='Bank account number' mod='npsprzelewy24'}</label>
            <input class="validate is_required form-control" data-validate="isNrb" type="text" id="iban" name="iban" autocomplete="off"
            value="{if isset($smarty.post.iban)}{$smarty.post.iban}{else}{if isset($company['iban'])}{$company['iban']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <div class="required form-group">
            <div class="checkbox">
                <label class="required" for="acceptance">
                <input type="checkbox" name="acceptance" id="acceptance" readonly="" {if isset($smarty.post.acceptance) && $smarty.post.acceptance == '1'}checked="checked"{/if} />
                {l s='Accept the' mod='npsprzelewy24'} <a href="{$p24_agreement_url}" target="_blank">{l s='“Regulations of Przelewy24”' mod='npsprzelewy24'}</a></label>
            </div>
        </div>
        <button type="submit" name="submitCompany" class="btn btn-default button button-medium pull-right"><span>{l s='Register' mod='npsprzelewy24'} <i class="icon-save right"></i></span></button>
    </form>
</div>