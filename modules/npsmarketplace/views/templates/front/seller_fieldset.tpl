{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<fieldset id="seller_profile">
    <div class="form-group">
        <label for="">{l s='Company Logo' mod='npsmarketplace'}</label></br>
        <img d="company_log_img" {if $seller['image']}src="{$seller['image']}?{time()}"{/if}/>
        <input id="company_logo" type="file" name="image">
    </div>
    <div class="form-group">
        <label class="required" for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
        <input class="validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name" required=""
        value="{if isset($smarty.post.seller_name)}{$smarty.post.seller_name}{else}{if isset($seller['name'])}{$seller['name']|escape:'html':'UTF-8'}{/if}{/if}"/>
    </div>
    <div class="form-group">
        <label class="required" for="company_name">{l s='Company Name' mod='npsmarketplace'}</label>
        <input class="validate form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name"
        value="{if isset($smarty.post.company_name)}{$smarty.post.company_name}{else}{if isset($seller['company_name'])}{$seller['company_name']|escape:'html':'UTF-8'}{/if}{/if}"/>
    </div>
    <div class="form-group">
        <label>{l s='Company Description' mod='npsmarketplace'}</label>
    </div>
    <ul class="nav nav-tabs" role="tablist">
      {foreach from=$languages item=lang}
          <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#shop_lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
      {/foreach}
    </ul>
    <div class="tab-content">
        {foreach from=$languages item=lang}
            <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="shop_lang{$lang.id_lang}">
                <div class="form-group">
                    <textarea class="tinymce form-control" name="company_description[{$lang.id_lang}]">{if isset($smarty.post.company_description[$lang.id_lang])}{$smarty.post.company_description[$lang.id_lang]}{else}{if isset($seller['company_description'][$lang.id_lang])}{$seller['company_description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="seller_phone">{l s='Phone Number' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isPhoneNumber" type="tel" id="seller_phone" name="seller_phone" required=""
            value="{if isset($smarty.post.seller_phone)}{$smarty.post.seller_phone}{else}{if isset($seller['phone'])}{$seller['phone']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="seller_email">{l s='Buisness Email' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isEmail" type="text" id="seller_email" name="seller_email" required=""
            value="{if isset($smarty.post.seller_email)}{$smarty.post.seller_email}{else}{if isset($seller['email'])}{$seller['email']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label for="seller_nip">{l s='NIP' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isNip" type="number" id="seller_nip" name="seller_nip"
            value="{if isset($smarty.post.seller_nip)}{$smarty.post.seller_nip}{else}{if isset($seller['nip'])}{$seller['nip']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label for="seller_regon">{l s='Regon' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isRegon" type="number" id="seller_regon" name="seller_regon"
            value="{if isset($smarty.post.seller_regon)}{$smarty.post.seller_regon}{else}{if isset($seller['regon'])}{$seller['regon']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="regulations_active">{l s='Add Company Regulations' mod='npsmarketplace'}</label>
        <input class="form-control" type="checkbox" id="regulations_active" name="regulations_active" {if $seller['regulations_active'] == 1}checked{/if}/>
    </div>
    <div class="form-group">
        <label>{l s='Regulations' mod='npsmarketplace'}</label>
    </div>
    <ul class="nav nav-tabs" role="tablist">
      {foreach from=$languages item=lang}
          <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#shop_lang_reg{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
      {/foreach}
    </ul>
    <div class="tab-content">
        {foreach from=$languages item=lang}
            <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="shop_lang_reg{$lang.id_lang}">
                <div class="form-group">
                    <textarea class="tinymce form-control" name="regulations[{$lang.id_lang}]">{if isset($smarty.post.regulations[$lang.id_lang])}{$smarty.post.regulations[$lang.id_lang]}{else}{if isset($seller['regulations'][$lang.id_lang])}{$seller['regulations'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>
<script type="text/javascript">
    $(".textarea-autosize").autosize();
</script>