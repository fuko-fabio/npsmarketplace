<fieldset id="seller_profile">
    <div class="form-group">
        <label for="">{l s='Company Logo' mod='npsmarketplace'}</label></br>
        <img d="company_log_img" {if $seller['image']}src="{$seller['image']}?{time()}"{/if}/>
        <input id="company_logo" type="file" name="image">
    </div>
    <div class="form-group">
        <label class="required" for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
        <input class="validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name" value="{if isset($seller['name'])}{$seller['name']|escape:'html':'UTF-8'}{/if}"/>
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
                    <label class="required" for="company_name">{l s='Company Name' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name[{$lang.id_lang}]" value="{if isset($seller['company_name'][$lang.id_lang])}{$seller['company_name'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}"/>
                </div>
                <div class="form-group">
                    <label for="company_description">{l s='Company Description' mod='npsmarketplace'}</label>
                    <textarea class="validate form-control rte" data-validate="isMessage" id="company_description" name="company_description[{$lang.id_lang}]" rows="6">{if isset($seller['company_description'][$lang.id_lang])}{$seller['company_description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="seller_phone">{l s='Phone Number' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isPhoneNumber" type="tel" id="seller_phone" name="seller_phone" required="" value="{if isset($seller['phone'])}{$seller['phone']|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="seller_email">{l s='Buisness Email' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isEmail" type="text" id="seller_email" name="seller_email" required="" value="{if isset($seller['email'])}{$seller['email']|escape:'html':'UTF-8'}{/if}"/>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label for="seller_nip">{l s='NIP' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isNip" type="number" id="seller_nip" name="seller_nip" value="{if isset($seller['nip'])}{$seller['nip']|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label for="seller_regon">{l s='Regon' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isRegon" type="number" id="seller_regon" name="seller_regon" value="{if isset($seller['regon'])}{$seller['regon']|escape:'html':'UTF-8'}{/if}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="regulations_active">{l s='Add Company Regulations' mod='npsmarketplace'}</label>
        <input class="form-control" type="checkbox" id="regulations_active" name="regulations_active" {if $seller['regulations_active'] == 1}checked{/if}/>
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
                    <label for="company_regulations">{l s='Regulations' mod='npsmarketplace'}</label>
                    <textarea class="form-control rte" id="company_regulations" name="regulations[{$lang.id_lang}]" rows="10">{if isset($seller['regulations'][$lang.id_lang])}{$seller['regulations'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>