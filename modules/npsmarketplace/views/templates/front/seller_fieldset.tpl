{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<fieldset id="seller_profile">
    <div class="box">
        <h3 class="page-heading">{l s='Shop informations' mod='npsmarketplace'}</h3>
        <div class="form-group">
            <label for="">{l s='Shop Logo' mod='npsmarketplace'}</label></br>
            <img d="company_log_img" {if $seller['image']}src="{$seller['image']}?{time()}"{/if}/>
            <input id="company_logo" type="file" name="image">
        </div>
        <div class="form-group">
            <label class="required" for="seller_name">{l s='Seller Name' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isGenericName" type="text" id="seller_name" name="seller_name" required=""
            value="{if isset($smarty.post.seller_name)}{$smarty.post.seller_name}{else}{if isset($seller['name'])}{$seller['name']|escape:'html':'UTF-8'}{/if}{/if}"/>
            <span class="form_info">{l s='This name will be visible for our customers' mod='npsmarketplace'}</span>
        </div>
        <ul class="nav nav-tabs {if $languages|@count < 2}hidden{/if}" role="tablist">
          {foreach from=$languages item=lang}
              <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#shop_lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
          {/foreach}
        </ul>
        <div class="tab-content">
            {foreach from=$languages item=lang}
                <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="shop_lang{$lang.id_lang}">
                    <div class="form-group">
                        <label>{l s='Shop Description' mod='npsmarketplace'}</label>
                        <textarea class="tinymce form-control" name="company_description[{$lang.id_lang}]">{if isset($smarty.post.company_description[$lang.id_lang])}{$smarty.post.company_description[$lang.id_lang]}{else}{if isset($seller['company_description'][$lang.id_lang])}{$seller['company_description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}{/if}</textarea>
                    </div>
                    <div class="form-group">
                        <label>{l s='Shop Regulations' mod='npsmarketplace'}</label>
                        <textarea class="tinymce form-control" name="regulations[{$lang.id_lang}]">{if isset($smarty.post.regulations[$lang.id_lang])}{$smarty.post.regulations[$lang.id_lang]}{else}{if isset($seller['regulations'][$lang.id_lang])}{$seller['regulations'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}{/if}</textarea>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="box">
        <h3 class="page-heading">{l s='Company informations' mod='npsmarketplace'}</h3>
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
        <div class="row">
            <div class="form-group col-md-6">
                <label for="seller_krs">{l s='KRS' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isInteger" type="number" id="seller_krs" name="seller_krs"
                value="{if isset($smarty.post.seller_krs)}{$smarty.post.seller_krs}{else}{if isset($seller['krs'])}{$seller['krs']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
            <div class="form-group col-md-6">
                <label for="seller_krs_reg">{l s='The KRS registration authority' mod='npsmarketplace'}</label>
                <input class="form-control" type="text" id="seller_krs_reg" name="seller_krs_reg"
                value="{if isset($smarty.post.seller_krs_reg)}{$smarty.post.seller_krs_reg}{else}{if isset($seller['krs_reg'])}{$seller['krs_reg']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        {include file="$address_tpl_path"}
    </div>
</fieldset>
<script type="text/javascript">
    $(".textarea-autosize").autosize();
</script>