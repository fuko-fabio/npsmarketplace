{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div>
		{assign var="stateExist" value=false}
		{assign var="postCodeExist" value=false}
		{assign var="dniExist" value=false}
		{assign var="homePhoneExist" value=false}
		{assign var="mobilePhoneExist" value=false}
		{assign var="atLeastOneExists" value=false}
        <ul class="grid row">
		{foreach from=$ordered_adr_fields item=field_name}
            <li class="col-xs-12 col-sm-6">
			{if $field_name eq 'company'}
				<div class="form-group">
					<label for="company">{l s='Company' mod='npsmarketplace'}</label>
					<input class="form-control validate" data-validate="{$address_validation.$field_name.validate}" type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{else}{if isset($address->company)}{$address->company|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'vat_number' && $vat_display}
				<div id="vat_area" class="hidden">
					<div id="vat_number">
						<div class="form-group">
							<label for="vat-number">{l s='VAT number' mod='npsmarketplace'}</label>
							<input type="text" class="form-control validate" data-validate="{$address_validation.$field_name.validate}" id="vat-number" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'html':'UTF-8'}{/if}{/if}" />
						</div>
					</div>
				</div>
			{elseif $field_name eq 'dni'}
                {assign var="dniExist" value=true}
                <div class="required form-group">
                    <label for="dni">{l s='Identification number' mod='npsmarketplace'}</label>
                    <input class="form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html'}{/if}{/if}" />
                    <span class="form_info">{l s='DNI / NIF / NIE' mod='npsmarketplace'}</span>
                </div>
			{elseif $field_name eq 'firstname'}
				<div class="required form-group">
					<label for="firstname">{l s='First name' mod='npsmarketplace'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{else}{if isset($address->firstname)}{$address->firstname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'lastname'}
				<div class="required form-group">
					<label for="lastname">{l s='Last name' mod='npsmarketplace'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{else}{if isset($address->lastname)}{$address->lastname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'address1'}
				<div class="required form-group">
					<label for="address1">{l s='Address' mod='npsmarketplace'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{else}{if isset($address->address1)}{$address->address1|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'address2'}
				<div class="required form-group">
					<label for="address2">{l s='Address (Line 2)' mod='npsmarketplace'}</label>
					<input class="validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{else}{if isset($address->address2)}{$address->address2|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'postcode'}
				{assign var="postCodeExist" value=true}
				<div class="required postcode form-group">
					<label for="postcode">{l s='Zip/Postal Code' mod='npsmarketplace'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{elseif $field_name eq 'city'}
				<div class="required form-group">
					<label for="city">{l s='City'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($address->city)}{$address->city|escape:'html':'UTF-8'}{/if}{/if}" maxlength="64" />
				</div>
				{* if customer hasn't update his layout address, country has to be verified but it's deprecated *}
			{elseif $field_name eq 'Country:name' || $field_name eq 'country'}
				<div class="required form-group">
					<label for="id_country">{l s='Country'}<sup>*</sup></label>
					<select id="id_country" class="form-control" name="id_country">{$countries_list}</select>
				</div>
			{elseif $field_name eq 'State:name'}
				{assign var="stateExist" value=true}
				<div class="required id_state form-group">
					<label for="id_state">{l s='State' mod='npsmarketplace'} <sup>*</sup></label>
					<select name="id_state" id="id_state" class="form-control">
						<option value="">-</option>
					</select>
				</div>
			{elseif $field_name eq 'phone'}
				{assign var="homePhoneExist" value=true}
				<div class="form-group phone-number">
					<label for="phone">{l s='Home phone' mod='npsmarketplace'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
				</div>
			{elseif $field_name eq 'phone_mobile'}
				{assign var="mobilePhoneExist" value=true}
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
					<label for="phone_mobile">{l s='Mobile phone' mod='npsmarketplace'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
            </li>
		{/foreach}
		{if !$postCodeExist}
            <li class="col-xs-12 col-sm-6">
			<div class="required postcode form-group unvisible">
				<label for="postcode">{l s='Zip/Postal Code' mod='npsmarketplace'} <sup>*</sup></label>
				<input class="is_required validate form-control" data-validate="{$address_validation.postcode.validate}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
            </li>
		{/if}
		{if !$stateExist}
            <li class="col-xs-12 col-sm-6">
			<div class="required id_state form-group unvisible">
				<label for="id_state">{l s='State' mod='npsmarketplace'} <sup>*</sup></label>
				<select name="id_state" id="id_state" class="form-control">
					<option value="">-</option>
				</select>
			</div>
            </li>
		{/if}
		{if !$dniExist}
            <li class="col-xs-12 col-sm-6">
			<div class="required dni form-group unvisible">
				<label for="dni">{l s='Identification number' mod='npsmarketplace'} <sup>*</sup></label>
				<input class="is_required form-control" data-validate="{$address_validation.dni.validate}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html'}{/if}{/if}" />
				<span class="form_info">{l s='DNI / NIF / NIE' mod='npsmarketplace'}</span>
			</div>
            </li>
		{/if}
        </ul>
		{if !$homePhoneExist}
			<div class="form-group phone-number">
				<label for="phone">{l s='Home phone' mod='npsmarketplace'}</label>
				<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
			</div>
		{/if}
		<div class="clearfix"></div>
		{if !$mobilePhoneExist}
			<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
				<label for="phone_mobile">{l s='Mobile phone' mod='npsmarketplace'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
				<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
		{/if}
        <div class="clearfix"></div>

        <p class="required"><sup>*</sup>{l s='Required field'}</p>
        {if isset($one_phone_at_least) && $one_phone_at_least}
            {assign var="atLeastOneExists" value=true}
            <p class="inline-infos required"><sup>**</sup> {l s='You must register at least one phone number.' mod='npsmarketplace'}</p>
        {/if}
</div>
{strip}
{if isset($smarty.post.id_state) && $smarty.post.id_state}
	{addJsDef idSelectedState=$smarty.post.id_state|intval}
{else if isset($address->id_state) && $address->id_state}
	{addJsDef idSelectedState=$address->id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($smarty.post.id_country) && $smarty.post.id_country}
	{addJsDef idSelectedCountry=$smarty.post.id_country|intval}
{else if isset($address->id_country) && $address->id_country}
	{addJsDef idSelectedCountry=$address->id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
	{addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
{/if}
{/strip}