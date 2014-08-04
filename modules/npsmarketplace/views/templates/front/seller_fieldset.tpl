<fieldset id="seller_profile">
    <div class="form-group">
        <label for="">{l s='Company Logo' mod='npsmarketplace'}</label></br>
        <img d="company_log_img" {if $seller['image']}src="{$seller['image']}?{time()}"{/if}/>
        <input id="company_logo" type="file" name="image">
    </div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      {foreach from=$languages item=lang}
          <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#shop_lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
      {/foreach}
    </ul>
        <!-- Tab panes -->
    <div class="tab-content">
        {foreach from=$languages item=lang}
            <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="shop_lang{$lang.id_lang}">
                <div class="form-group">
                    <label class="required" for="company_name">{l s='Company Name' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="company_name" name="company_name[{$lang.id_lang}]" required="" value="{if isset($seller['company_name'][$lang.id_lang])}{$seller['company_name'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}"/>
                </div>
                <div class="form-group">
                    <label class="required" for="company_description">{l s='Company Description' mod='npsmarketplace'}</label>
                    <textarea class="validate form-control" data-validate="isMessage" id="company_description" name="company_description[{$lang.id_lang}]" rows="6">{if isset($seller['company_description'][$lang.id_lang])}{$seller['company_description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
                <div class="form-group">
                    <label class="required" for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name[{$lang.id_lang}]" required="" value="{if isset($seller['name'][$lang.id_lang])}{$seller['name'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}"/>
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
            <input class="validate form-control" data-validate="isNip" type="number" id="seller_nip" name="seller_nip" required="" value="{if isset($seller['nip'])}{$seller['nip']|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label for="seller_regon">{l s='Regon' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isRegon" type="number" id="seller_regon" name="seller_regon" required="" value="{if isset($seller['regon'])}{$seller['regon']|escape:'html':'UTF-8'}{/if}"/>
        </div>
    </div>
</fieldset>